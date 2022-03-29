<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use ErikPDev\AdvanceDeaths\discord\discordListener;
use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use JaxkDev\DiscordBot\Models\Messages\Message;

class topkills extends simpleCommand {

	public function run(Message $message, array $args): void {

		$data = databaseProvider::$data["kills"];

		$fields = [];

		foreach ($data as $place => $playerData) {
			$fields[($place + 1) . ". " . $playerData["PlayerName"]] = $playerData["Kills"] . " kills";
		}

		discordListener::sendEmbeddedMessage($message->getChannelId(), $this->templateData["title"], "", $fields, $this->templateData["color"]);

	}

}