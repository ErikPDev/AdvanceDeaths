<?php

namespace ErikPDev\AdvanceDeaths\utils\scriptModules;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class playSound extends module {

	private PlaySoundPacket $sound;

	public function __construct(string $soundID, protected string $playerWanted) {

		$this->sound = PlaySoundPacket::create(
			$soundID,
			0,
			0,
			0,
			1,
			1
		);

	}

	public function run($entity) {

		if (!$entity instanceof Player) return;
		/** @var Player $entity */
		$sound = clone $this->sound;
		$sound->x = $entity->getPosition()->x;
		$sound->y = $entity->getPosition()->y;
		$sound->z = $entity->getPosition()->z;

		Server::getInstance()->broadcastPackets($entity->getWorld()->getPlayers(), [$sound]);

	}

}