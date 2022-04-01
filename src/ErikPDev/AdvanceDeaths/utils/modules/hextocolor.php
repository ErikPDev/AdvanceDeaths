<?php

namespace ErikPDev\AdvanceDeaths\utils\modules;

use pocketmine\color\Color;

class hextocolor {

	/**
	 * Convert Hexadec to Color
	 * @param $color
	 * @param false $opacity
	 * @return Color
	 */
	static function convert($color, $opacity = false): Color {

		$default = new Color(0, 0, 0);

		//Return default if no color provided
		if (empty($color))
			return $default;

		//Sanitize $color if "#" is provided
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3) {
			$hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else {
			return $default;
		}

		//Convert hexadec to Color
		$rgb = array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if ($opacity) {
			if (abs($opacity) > 1)
				$opacity = 1.0;
			$output = new Color($rgb[0], $rgb[1], $rgb[2], $opacity * 255);
		} else {
			$output = new Color($rgb[0], $rgb[1], $rgb[2]);
		}

		//Return rgb(a) color string
		return $output;
	}
}