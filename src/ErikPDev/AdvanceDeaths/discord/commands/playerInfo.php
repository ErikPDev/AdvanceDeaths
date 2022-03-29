<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use ErikPDev\AdvanceDeaths\discord\discordListener;
use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use JaxkDev\DiscordBot\Models\Messages\Message;

class playerInfo extends simpleCommand {

	public function run(Message $message, array $args): void {

		if (count($args) == 0) {
			discordListener::sendEmbeddedMessage($message->getChannelId(), "❌ Insufficient args", "Usage: " . discordListener::$prefix . $this->getCommandName(), [], 16711680);
			return;
		}

		$responsePromise = databaseProvider::getAll(str_replace("%", "", $args[0]) . "%");
		$responsePromise->onCompletion(
			function (array $data) use ($message) {
				$formattedResponse = "";
				foreach ($data as $key => $value) {
					if ($key == "PlayerName") continue;
					$formattedResponse .= "$key: $value\n";
				}
				discordListener::sendEmbeddedMessage($message->getChannelId(), str_replace("{name}", $data["PlayerName"], $this->templateData["title"]), $formattedResponse, [], $this->templateData["color"]);
			},
			function () use ($message) {
				discordListener::sendEmbeddedMessage($message->getChannelId(), "❌ Error", "Player not found.", [], 16711743);
			}
		);

	}

}