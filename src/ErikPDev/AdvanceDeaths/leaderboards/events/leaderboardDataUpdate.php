<?php

namespace ErikPDev\AdvanceDeaths\leaderboards\events;

use pocketmine\event\Event;

class leaderboardDataUpdate extends Event {

	public function __construct(protected array $data) {}

	public function getData() {

		return $this->data;

	}

}