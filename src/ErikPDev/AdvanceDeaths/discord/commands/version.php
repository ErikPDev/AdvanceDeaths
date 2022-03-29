<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use ErikPDev\AdvanceDeaths\ADMain;
use ErikPDev\AdvanceDeaths\discord\discordListener;
use JaxkDev\DiscordBot\Models\Messages\Message;
use pocketmine\Server;

class version extends simpleCommand {

	public function run(Message $message, array $args): void {

		discordListener::sendEmbeddedMessage(
			$message->getChannelId(),
		"Version",
			ADMain::getInstance()->getDescription()->getFullName()." ".Server::getInstance()->getApiVersion(),
			[],
			8388352
		);

	}

}