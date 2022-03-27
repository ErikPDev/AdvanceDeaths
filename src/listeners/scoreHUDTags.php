<?php


namespace ErikPDev\AdvanceDeaths\listeners;

use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;

class scoreHUDTags implements Listener {

	private array $tags = array(
		"advancedeaths.myDeaths",
		"advancedeaths.myKills",
		"advancedeaths.myKillstreaks",
		"advancedeaths.kdr"
	);

	public function onTagResolve(TagsResolveEvent $event) {

		$player = $event->getPlayer();
		$tag = $event->getTag();

		$playerName = $player->getName();
		$tagName = $tag->getName();

		if (!in_array($tagName, $this->tags)) return;
		$tag->setValue("Loading");

	}

	public function playerJoin(PlayerJoinEvent $event) {

		$this->updateTags($event->getPlayer());

	}

	public function onDeath(PlayerDeathEvent $event) {

		/** @var EntityDamageByEntityEvent|EntityDamageEvent $damageCause */
		$damageCause = $event->getEntity()->getLastDamageCause();

		if ($damageCause instanceof EntityDamageByEntityEvent) return;

		$this->updateTags($damageCause->getDamager());

	}

	public function onLevelChange(EntityTeleportEvent $event): void {

		$entity = $event->getEntity();
		if(!$entity instanceof Player) return;
		/** @var Player $entity */
		$this->updateTags($entity);

	}


	public function updateTags(Player $player) {

		$promise = databaseProvider::getAll($player->getName());

		$promise->onCompletion(function (array $data) use ($player) {
			var_dump($data);
			$kdr =
			$ev1 = new PlayerTagsUpdateEvent(
				$player,
				[
					new ScoreTag("advancedeaths.myDeaths", strval($data["Deaths"])),
					new ScoreTag("advancedeaths.myKills", strval($data["Kills"])),
					new ScoreTag("advancedeaths.myKillstreaks", strval($data["Killstreak"])),
					new ScoreTag("advancedeaths.kdr", strval(databaseProvider::getKillToDeathRatio($data["Kills"], $data["Deaths"])))
				]
			);
			$ev1->call();

		},
			function () {
			});

	}

}