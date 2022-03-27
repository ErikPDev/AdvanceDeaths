<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use ErikPDev\AdvanceDeaths\discord\discordListener;
use JaxkDev\DiscordBot\Models\Messages\Message;

class help extends simpleCommand {

	public function run(Message $message, array $args): void {

		$fields = [];
		foreach (discordListener::getCommands() as $commandName => $commandInstance) {
			$fields[discordListener::$prefix . $commandName] = $commandInstance->getCommandDesc();
		}

		discordListener::sendEmbeddedMessage($message->getChannelId(), "Help", "", $fields, 3070299);

	}

}