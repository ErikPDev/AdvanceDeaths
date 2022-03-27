<?php

namespace ErikPDev\AdvanceDeaths\utils;

use pocketmine\player\Player;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use pocketmine\Server;

class currencyManager {

	private int $pluginUsed = 0; # Possible values are 0="not set", 1="EconomyAPI", 2="BedrockEconomy", 3="Capital"

	public function __construct() {

		if (Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI") !== null) {
			$this->pluginUsed = 1;
		}
		if (Server::getInstance()->getPluginManager()->getPlugin("BedrockEconomy") !== null) {
			$this->pluginUsed = 2;
		}
		if (Server::getInstance()->getPluginManager()->getPlugin("Capital") !== null) {
			$this->pluginUsed = 3;
		}
		$this->checkSupported();

	}

	private function checkSupported(): void {

		if ($this->pluginUsed == 0) {
			throw new \ErrorException("No supported currency plugin found.");
		}

	}

	public function getMoney(Player $player): Promise {

		$this->checkSupported();

		$promise = new PromiseResolver();

		if ($this->pluginUsed == 1) {
			$EconomyAPI = \onebone\economyapi\EconomyAPI::getInstance();
			$promise->resolve($EconomyAPI->myMoney($player));
			return $promise->getPromise();
		}

		if ($this->pluginUsed == 2) {
			$BedrockEconomyAPI = \cooldogedev\BedrockEconomy\api\BedrockEconomyAPI::beta();
			$BedrockEconomyAPI->get($player->getName())->onCompletion(

				function (int $bal) use ($promise) {
					$promise->resolve($bal);
				},

				function () use ($promise) {
					$promise->reject();
				}

			);
			return $promise->getPromise();
		}

		return $promise->getPromise();

	}

	public function reduceMoney(Player $player, float $amount): void {

		$this->checkSupported();
		if ($this->pluginUsed == 1) {
			$EconomyAPI = \onebone\economyapi\EconomyAPI::getInstance();
			$EconomyAPI->reduceMoney($player, $amount, true, "AdvanceDeaths");
			return;
		}

		if ($this->pluginUsed == 2) {
			$BedrockEconomyAPI = \cooldogedev\BedrockEconomy\api\BedrockEconomyAPI::beta();
			$BedrockEconomyAPI->deduct($player->getName(), $amount, "AdvanceDeaths");
			return;
		}

	}

	public function addMoney(Player $player, float $amount): void {

		$this->checkSupported();
		if ($this->pluginUsed == 1) {
			$EconomyAPI = \onebone\economyapi\EconomyAPI::getInstance();
			$EconomyAPI->addMoney($player, $amount, true, "AdvanceDeaths");
			return;
		}

		if ($this->pluginUsed == 2) {
			$BedrockEconomyAPI = \cooldogedev\BedrockEconomy\api\BedrockEconomyAPI::beta();
			$BedrockEconomyAPI->add($player->getName(), $amount, "AdvanceDeaths");
			return;
		}

	}

}