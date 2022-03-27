<?php

namespace ErikPDev\AdvanceDeaths\utils;

use ErikPDev\AdvanceDeaths\ADMain;
use ErrorException;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\player\Player;
use pocketmine\utils\Config;


class deathTranslate {

	private array $deriveMessages;

	/**
	 * @throws ErrorException
	 */
	public function __construct() {

		$deathMessagesConfig = new Config(ADMain::getInstance()->getDataFolder() . "deathMessages.yml");
		if ($deathMessagesConfig->get("deathMessages") == null) throw new ErrorException("deathMessages is null, check your deathMessages.yml in plugin_data/AdvanceDeaths.");
		foreach ($deathMessagesConfig->getNested("deathMessages") as $derive => $message) {
			if (!is_string($message)) throw new ErrorException("Derive message isn't a string, check your deathMessages.yml in plugin_data/AdvanceDeaths.");
		}
		$this->deriveMessages = $deathMessagesConfig->getNested("deathMessages");

	}

	/**
	 * @param string $derive
	 * @param Living|Player $victim
	 * @param Living|Player|null $murderer
	 * @return string
	 */
	public function get(string $derive, Player|Living $victim, Living|Human|Player $murderer = null): string {

		$deriveMessage = str_replace("{victim}", $victim->getName(), $this->deriveMessages[$derive]);

		if ($murderer == null) return $deriveMessage;
		$deriveMessage = str_replace("{murdererHealth}", ($murderer->getHealth() . "/" . $murderer->getMaxHealth()), $deriveMessage);
		$deriveMessage = str_replace("{murderer}", $murderer->getName(), $deriveMessage);
		if(!$murderer instanceof Human || !$murderer instanceof Player) return $deriveMessage;
		/** @var Human $murderer */
		$deriveMessage = str_replace("{itemUsed}", $murderer->getInventory()->getItemInHand()->getName(), $deriveMessage);

		return $deriveMessage;

	}

}