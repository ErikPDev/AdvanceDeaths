<?php

namespace ErikPDev\AdvanceDeaths\utils\scriptModules;

abstract class module {

	protected string $playerWanted;

	public function getPlayerWanted(): string {

		return $this->playerWanted;

	}

}