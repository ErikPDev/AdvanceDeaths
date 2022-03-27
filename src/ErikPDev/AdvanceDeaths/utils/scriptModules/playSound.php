<?php

namespace ErikPDev\AdvanceDeaths\utils\scriptModules;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class playSound {

	private PlaySoundPacket $sound;

	public function __construct(string $soundID, private string $playerWanted) {

		$this->sound = PlaySoundPacket::create(
			$soundID,
			0,
			0,
			0,
			1,
			1
		);

	}

	public function getPlayerWanted(): string {

		return $this->playerWanted;

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