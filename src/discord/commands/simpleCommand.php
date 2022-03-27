<?php

namespace ErikPDev\AdvanceDeaths\discord\commands;

use JaxkDev\DiscordBot\Models\Messages\Message;
use JaxkDev\DiscordBot\Plugin\Api;

abstract class simpleCommand {

	public function __construct(private string $commandName, private string $commandDesc, protected Api $api, protected $templateData) {
	}

	public function getCommandName(): string {

		return $this->commandName;

	}

	public function getCommandDesc(): string {

		return $this->commandDesc;

	}

	abstract public function run(Message $message, array $args): void;

}