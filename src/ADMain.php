<?php

namespace ErikPDev\AdvanceDeaths;

use ErikPDev\AdvanceDeaths\commands\ads;
use ErikPDev\AdvanceDeaths\discord\discordListener;
use ErikPDev\AdvanceDeaths\listeners\instantRespawn;
use ErikPDev\AdvanceDeaths\listeners\moneyRelated\deathMoney;
use ErikPDev\AdvanceDeaths\listeners\moneyRelated\killMoney;
use ErikPDev\AdvanceDeaths\listeners\scoreHUDTags;
use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use ErikPDev\AdvanceDeaths\utils\deathTranslate;
use ErikPDev\AdvanceDeaths\utils\scriptModules\scriptToData;
use ErikPDev\AdvanceDeaths\utils\translationContainer;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class ADMain extends PluginBase implements Listener {

	private deathTranslate $deathTranslate;
	private static ADMain $instance;
	private array $onDeathScript;

	protected function onEnable(): void {

		$this->saveResources();

		self::$instance = $this;

		$this->deathTranslate = new deathTranslate();
		new translationContainer();

		Server::getInstance()->getPluginManager()->registerEvents($this, $this);
		Server::getInstance()->getPluginManager()->registerEvents(new databaseProvider(), $this);

		$this->loadFeatures();

		$this->loadScripts();

		$this->getServer()->getCommandMap()->register("ads", new ads());

	}

	public function onDisable(): void {

		databaseProvider::close();

	}

	public static function getInstance(): ADMain {

		return self::$instance;

	}

	private function saveResources(): void {

		foreach ($this->getResources() as $resourceName => $data) {
			if ($resourceName == "sqlite.sql") continue;
			$this->saveResource($resourceName, false);
		}

		$this->reloadConfig();

	}

	private function loadFeatures(): void {

		if ($this->getConfig()->get("instant-respawn", false) == true) {
			Server::getInstance()->getPluginManager()->registerEvents(new instantRespawn(), $this);
		}

		if ($this->getConfig()->getNested("onDeathMoney")["isEnabled"] == true) {
			Server::getInstance()->getPluginManager()->registerEvents(new deathMoney($this->getConfig()->getNested("onDeathMoney")), $this);
		}

		if ($this->getConfig()->getNested("onKillMoney")["isEnabled"] == true) {
			Server::getInstance()->getPluginManager()->registerEvents(new killMoney($this->getConfig()->getNested("onKillMoney")), $this);
		}

		$discordBotConfig = new Config($this->getDataFolder() . "discordBot.yml");
		if ($discordBotConfig->get("isEnabled") == true) {
			if (Server::getInstance()->getPluginManager()->getPlugin("DiscordBot") == null)
				throw new \ErrorException("DiscordBot is set to enabled but the DiscordBot plugin is not found.");
			Server::getInstance()->getPluginManager()->registerEvents(new discordListener(), $this);
		}

		if (Server::getInstance()->getPluginManager()->getPlugin("ScoreHud") !== null) {
			Server::getInstance()->getPluginManager()->registerEvents(new scoreHUDTags(), $this);
		}

	}

	private function loadScripts(): void {

		$onDeathScriptPath = $this->getDataFolder() . "scripts/onDeathScript.yml";

		if (!file_exists($onDeathScriptPath)) {
			$this->getLogger()->info("AdvanceDeaths couldn't find the onDeathScript located on plugin_data/AdvanceDeaths/scripts");
			return;
		}

		$onDeathScript = yaml_parse_file($onDeathScriptPath);

		if (!isset($onDeathScript["script"])) {
			$this->getLogger()->info("The formatting of onDeathScript is incorrect.");
			return;
		}

		$this->onDeathScript = [];
		foreach ($onDeathScript["script"] as $id => $value) {
			$functionData = scriptToData::decode($value);
			if ($functionData == false) continue;
			$this->onDeathScript[] = $functionData;
		}

	}

	private function runDeathScript(Player $player): void {

		$deathByEntity = false;
		$damageCause = $player->getLastDamageCause();
		if ($damageCause instanceof EntityDamageByEntityEvent) {
			$deathByEntity = true;
		}


		foreach ($this->onDeathScript as $function) {
			$functionPlayer = $player;
			if (strtolower($function->getPlayerWanted()) == "playerkiller") {
				if ($deathByEntity == false) return;
				/** @var EntityDamageByEntityEvent $damageCause */
				$functionPlayer = $damageCause->getDamager();
			}

			$function->run($functionPlayer);
		}

	}

	/**
	 * @priority LOWEST
	 */
	public function deathEvent(PlayerDeathEvent $event) {

		$this->runDeathScript($event->getPlayer());
		/** @var EntityDamageByEntityEvent|EntityDamageEvent $damageCause */
		$damageCause = $event->getEntity()->getLastDamageCause();
		$damager = null;

		if ($damageCause instanceof EntityDamageByEntityEvent) {
			$damager = $damageCause->getDamager();
		}

		$event->setDeathMessage($this->deathTranslate->get(
			PlayerDeathEvent::deriveMessage($event->getPlayer()->getDisplayName(), $damageCause)->getText(),
			$event->getPlayer(),
			$damager
		));


		if ($this->getConfig()->getNested("killstreakAnnouncements")["isEnabled"] == false) return;
		if($damager == null) return;

		$promise = databaseProvider::getKillstreaks($damager->getName());
		$promise->onCompletion(
			function ($data) use ($event, $damager) {
				$killstreak = $data["Killstreak"];
				$intervalKill = $this->getConfig()->getNested("killstreakAnnouncements")["annonuceEveryXKillstreaks"];
				$isMultiple = !str_contains(strval($killstreak / $intervalKill), ".");
				if (!$isMultiple) return;
				$translation = translationContainer::translate("killstreakAnnouncement", true, [
					"1" => $damager->getName(),
					"2" => $killstreak
				]);
				Server::getInstance()->broadcastMessage($translation);
			},
			function () {
			}
		);

	}

}
