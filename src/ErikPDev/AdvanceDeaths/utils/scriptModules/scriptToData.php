<?php

namespace ErikPDev\AdvanceDeaths\utils\scriptModules;

use ErikPDev\AdvanceDeaths\ADMain;

class scriptToData {

	public static function decode(string $word): mixed {

		if (!preg_match("/[a-zA-Z]+\\([^)]*\\)/i", $word)) return false;

		preg_match("/\\([^)]*\\)/i", $word, $variables);
		$variables = explode(",", substr(rtrim($variables[0], ")"), 1));

		foreach ($variables as $variableID => $variable) {
			$variables[$variableID] = trim(strtolower($variable));
		}

		$function = explode("(", $word)[0];

		$results = false;
		switch ($function) {

			case "particle":
				$particleFilePath = ADMain::getInstance()->getDataFolder() . "scripts/particles/$variables[0].json";

				if (!file_exists($particleFilePath)) {
					ADMain::getInstance()->getLogger()->critical("Particle file not found.");
					$results = false;
					break;
				}

				$fileContents = file_get_contents($particleFilePath);

				$jsonData = json_decode($fileContents);
				if (is_null($jsonData)) {
					ADMain::getInstance()->getLogger()->critical("Failed decoding the json file.");
					$results = false;
					break;
				}

				$results = new Particle($jsonData, $variables[1]);

				break;

			case "playsound":

				$results = new playSound($variables[0], $variables[1]);

				break;

			case "message":

				$results = new message($variables[0], $variables[1]);

				break;

			case "heal":

				$results = new heal($variables[0], $variables[1]);

		}

		return $results;

	}

}