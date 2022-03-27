<?php

namespace ErikPDev\AdvanceDeaths\utils;

use ErikPDev\AdvanceDeaths\ADMain;

class translationContainer {

	private static array $translations;

	public function __construct() {

		self::$translations = yaml_parse_file(ADMain::getInstance()->getDataFolder() . "lang.yml");

	}

	public static function translate(string $translationString, bool $showPrefix, array $params): array|string {

		$prefix = "";
		if ($showPrefix) {
			$prefix = "§bAdvance§cDeaths §6>§r ";
		}

		if (!array_key_exists($translationString, self::$translations)) return "null";

		$translated = $prefix . "" . self::$translations[$translationString];

		foreach ($params as $transKey => $transValue) {
			$translated = str_replace("%" . $transKey, $transValue, $translated);
		}

		return $translated;

	}

}