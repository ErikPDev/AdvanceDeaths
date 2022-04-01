<?php

namespace ErikPDev\AdvanceDeaths\utils\scriptModules;

use pocketmine\player\Player;

class heal extends module{

	public function __construct(public string $message, protected string $playerWanted) {}

	public function run($entity) {

		if (!$entity instanceof Player) return;
		$entity->setHealth($entity->getMaxHealth());

	}

}