<?php

namespace ErikPDev\AdvanceDeaths\listeners\moneyRelated;

use ErikPDev\AdvanceDeaths\utils\currencyManager;
use ErikPDev\AdvanceDeaths\utils\translationContainer;
use ErrorException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;

class deathMoney implements Listener {

	private currencyManager $currencyManager;

	private static array $wordTranslation = array(
		"gain" => "gained",
		"lose" => "lost"
	);

	public function __construct(private array $configuration) {

		$this->currencyManager = new currencyManager();

	}

	private function modifyMoney(Player $player, float $amount, string $type) {

		switch (strtolower($type)) {
			case "lose":
				$this->currencyManager->reduceMoney($player, $amount);
				break;

			case "gain":
				$this->currencyManager->addMoney($player, $amount);
				break;

			default:
				throw new ErrorException("Unknown value type at deathMoney, check your configuration.");
		}

	}

	public function deathEvent(PlayerDeathEvent $event) {

		$player = $event->getPlayer();
		$this->currencyManager->getMoney($player)->onCompletion(
			function (int|float $balance) use ($player) {
				$amount = match ($this->configuration["type"]) {
					"playerMoney" => $this->currencyManager->getMoney($player),
					"amount" => $this->configuration["amount"],
					"percent" => ($this->configuration["amount"] / 100) * $balance,
					default => throw new ErrorException("Death Type is invalid, check your configuration."),
				};

				$this->modifyMoney($player, $amount, $this->configuration["valueType"]);
				$player->sendMessage(translationContainer::translate("deathMoney", true, array("1" => self::$wordTranslation[$this->configuration["valueType"]], "2" => $amount)));

			},
			function () {
			});
	}

}