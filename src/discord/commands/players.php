<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use ErikPDev\AdvanceDeaths\discord\discordListener;
use JaxkDev\DiscordBot\Models\Messages\Message;
use pocketmine\Server;

class players extends simpleCommand {

	public function run(Message $message, array $args): void {

		$onlinePlayers = Server::getInstance()->getOnlinePlayers();

		$playersFormatted = "";
		$playerList = 0;
		foreach ($onlinePlayers as $onlinePlayer) {
			$playerList++;
			$playersFormatted .= "$playerList - " . $onlinePlayer->getName() . "\n";
		}

		discordListener::sendEmbeddedMessage(
			$message->getChannelId(),
			str_replace("{count}", count($onlinePlayers), $this->templateData["title"]),
			$playersFormatted,
			[],
			$this->templateData["color"]
		);

	}

}