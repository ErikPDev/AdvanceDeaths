<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use ErikPDev\AdvanceDeaths\discord\discordListener;
use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use JaxkDev\DiscordBot\Models\Messages\Message;

class topkillstreaks extends simpleCommand {

	public function run(Message $message, array $args): void {

		$data = databaseProvider::$data["Killstreak"];

		$fields = [];

		foreach ($data as $place => $playerData) {
			$fields[($place + 1) . ". " . $playerData["PlayerName"]] = $playerData["Killstreak"] . " killstreaks";
		}

		discordListener::sendEmbeddedMessage($message->getChannelId(), $this->templateData["title"], "", $fields, $this->templateData["color"]);


	}

}