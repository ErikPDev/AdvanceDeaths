<?php

namespace ErikPDev\AdvanceDeaths;

use ErikPDev\AdvanceDeaths\commands\ads;
use ErikPDev\AdvanceDeaths\leaderboards\events\leaderboardClose;
use ErikPDev\AdvanceDeaths\leaderboards\leaderboard;
use ErikPDev\AdvanceDeaths\listeners\instantRespawn;
use ErikPDev\AdvanceDeaths\listeners\moneyRelated\deathMoney;
use ErikPDev\AdvanceDeaths\listeners\moneyRelated\killMoney;
use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use ErikPDev\AdvanceDeaths\utils\deathTranslate;
use ErikPDev\AdvanceDeaths\utils\scriptModules\scriptToData;
use ErikPDev\AdvanceDeaths\utils\translationContainer;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\particle\BlockBreakParticle;

class ADMain extends PluginBase implements Listener {

	private deathTranslate $deathTranslate;
	private static ADMain $instance;
	private array $onDeathScript;

	private BlockBreakParticle $bloodFXParticle;

    /**
     * @throws \ErrorException
     */
    protected function onEnable(): void {

		$this->saveResources();

		if ($this->getConfig()->get("config-version", 1) !== 4) {
			$this->getLogger()->critical("Your configuration is outdated. Delete or rename the old one to update it.");
			Server::getInstance()->getPluginManager()->disablePlugin($this);
			return;
		}

		self::$instance = $this;

		$this->deathTranslate = new deathTranslate();
		new translationContainer();

		Server::getInstance()->getPluginManager()->registerEvents($this, $this);
		Server::getInstance()->getPluginManager()->registerEvents(new databaseProvider(), $this);

		$this->loadFeatures();

		$this->loadScripts();

		$this->bloodFXParticle = new BlockBreakParticle(VanillaBlocks::REDSTONE());

	}

	public function onDisable(): void {

		databaseProvider::close();
		$event = new leaderboardClose();
		$event->call();

	}

	public static function getInstance(): ADMain {

		return self::$instance;

	}

	private function saveResources(): void {

		foreach ($this->getResources() as $resourceName => $data) {
			if ($resourceName == "sqlite.sql") continue;
			$this->saveResource($resourceName);
		}

		$this->reloadConfig();

	}

    /**
     * @throws \ErrorException
     */
    private function loadFeatures(): void {

		if ($this->getConfig()->get("commandEnabled", true)){
			$this->getServer()->getCommandMap()->register("AdvanceDeaths", new ads());
		}

		if ($this->getConfig()->get("instant-respawn")) {
			Server::getInstance()->getPluginManager()->registerEvents(new instantRespawn(), $this);
		}

		if ($this->getConfig()->getNested("onDeathMoney")["isEnabled"]) {
			Server::getInstance()->getPluginManager()->registerEvents(new deathMoney($this->getConfig()->getNested("onDeathMoney")), $this);
		}

		if ($this->getConfig()->getNested("onKillMoney")["isEnabled"]) {
			Server::getInstance()->getPluginManager()->registerEvents(new killMoney($this->getConfig()->getNested("onKillMoney")), $this);
		}

		$leaderboardConfiguration = new Config($this->getDataFolder() . "leaderboards.yml");

		$killsConfiguration = $leaderboardConfiguration->getNested("kills");
		if ($killsConfiguration["isEnabled"]) {
			Server::getInstance()->getPluginManager()->registerEvents(new leaderboard($killsConfiguration, 0), $this);
		}

		$deathsConfiguration = $leaderboardConfiguration->getNested("deaths");
		if ($deathsConfiguration["isEnabled"]) {
			Server::getInstance()->getPluginManager()->registerEvents(new leaderboard($deathsConfiguration, 1), $this);
		}

		$killstreaksConfiguration = $leaderboardConfiguration->getNested("killstreaks");
		if ($killstreaksConfiguration["isEnabled"]) {
			Server::getInstance()->getPluginManager()->registerEvents(new leaderboard($killstreaksConfiguration, 2), $this);
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
			if (!$functionData) continue;
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
				if (!$deathByEntity) return;
				/** @var EntityDamageByEntityEvent $damageCause */
				$functionPlayer = $damageCause->getDamager();
			}

			$function->run($functionPlayer);
		}

	}

	public function entityDamage(EntityDamageEvent $event) {

		if($event->getEntity() instanceof ItemEntity) return;

		if ($this->getConfig()->get("bloodHit", true)) {

			$event->getEntity()->getWorld()->addParticle($event->getEntity()->getPosition()->add(0, 1, 0), $this->bloodFXParticle);

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


		if (!$this->getConfig()->getNested("killstreakAnnouncements")["isEnabled"]) return;
		if ($damager == null) return;
		if (!$damager instanceof Player) return;

		$promise = databaseProvider::getKillstreaks($damager->getName());
		$promise->onCompletion(
			function ($data) use ($damager) {
				/** @var Player $damager */
				$killstreak = $data["Killstreak"];
				$intervalKill = $this->getConfig()->getNested("killstreakAnnouncements")["annonuceEveryXKillstreaks"];
				$isMultiple = !str_contains(strval($killstreak / $intervalKill), ".");
				if (!$isMultiple) return;
				if ($killstreak == 0) return;
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
