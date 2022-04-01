<?php

namespace ErikPDev\AdvanceDeaths\utils\scriptModules;

abstract class module {

	protected string $playerWanted;

	protected function getPlayerWanted(): string {

		return $this->playerWanted;

	}

}