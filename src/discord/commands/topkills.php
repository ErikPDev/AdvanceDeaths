<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use ErikPDev\AdvanceDeaths\discord\discordListener;
use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use JaxkDev\DiscordBot\Models\Messages\Message;

class topkills extends simpleCommand {

	public function run(Message $message, array $args): void {

		$responsePromise = databaseProvider::getTop5kills();
		$responsePromise->onCompletion(
			function (array $data) use ($message) {
				$fields = [];
				foreach ($data as $place => $playerData) {
					$fields[($place + 1) . ". " . $playerData["PlayerName"]] = $playerData["Kills"] . " kills";
				}
				discordListener::sendEmbeddedMessage($message->getChannelId(), $this->templateData["title"], "", $fields, $this->templateData["color"]);
			},
			function () use ($message) {
				discordListener::sendEmbeddedMessage($message->getChannelId(), "❌ Error", "Something went wrong.", [], 16711743);
			}
		);

	}

}