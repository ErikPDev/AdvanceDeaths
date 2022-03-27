<?php

namespace ErikPDev\AdvanceDeaths\listeners;

use ErikPDev\AdvanceDeaths\ADMain;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\player\Player;

class instantRespawn implements Listener {

	/**
	 * @priority HIGHEST
	 * @ignoreCancelled false
	 * @param PlayerJoinEvent $event
	 */
	public function onJoin(PlayerJoinEvent $event): void {

		$player = $event->getPlayer();
		if (in_array($player->getWorld()->getFolderName(), ADMain::getInstance()->getConfig()->get("instant-respawn-disabled-worlds"))) return;

		$pk = GameRulesChangedPacket::create(
			["doimmediaterespawn" => new BoolGameRule(true, false)]
		);

		$player->getNetworkSession()->sendDataPacket($pk);

	}

	/**
	 * @priority HIGHEST
	 * @ignoreCancelled false
	 * @param EntityTeleportEvent $event
	 */
	public function onLevelChange(EntityTeleportEvent $event): void {

		$player = $event->getEntity();
		if (!$player instanceof Player) return;
		if (in_array($event->getTo()->getWorld()->getFolderName(), ADMain::getInstance()->getConfig()->get("instant-respawn-disabled-worlds"))) return;

		$pk = GameRulesChangedPacket::create(
			["doimmediaterespawn" => new BoolGameRule(true, false)]
		);

		$player->getNetworkSession()->sendDataPacket($pk);

	}

}