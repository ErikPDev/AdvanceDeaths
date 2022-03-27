<?php

namespace ErikPDev\AdvanceDeaths\utils\scriptModules;

use ErikPDev\AdvanceDeaths\ADMain;
use ErikPDev\AdvanceDeaths\utils\modules\hextocolor;
use pocketmine\math\Vector3;
use pocketmine\world\particle\DustParticle;
use stdClass;

class Particle {

	private array $particles;
	private array $particlesColor;

	public function __construct(private stdClass $particleData, private string $playerWanted) {

		if ($particleData->particleType !== "DustParticle") {
			ADMain::getInstance()->getLogger()->critical("ParticleType value is not supported.");
			throw new \ErrorException("ParticleType value is not supported.");
		}

		if ($particleData->particleRatio !== "square") {
			ADMain::getInstance()->getLogger()->critical("ParticleRatio value is not supported.");
			throw new \ErrorException("ParticleRatio value is not supported");
		}

		if (count($particleData->particles) !== 8) {
			ADMain::getInstance()->getLogger()->critical("Particles Y list is invalid.");
			throw new \ErrorException("Particles Y list is invalid");
		}

		$cancel = false;

		foreach ($particleData->particles as $Y => $colors) {
			if (count(explode(",", $colors)) !== 8) {
				$cancel = true;
			}
		}

		if ($cancel == true) {
			ADMain::getInstance()->getLogger()->critical("Particles Z list is invalid.");
			throw new \ErrorException("Particles Z list invalid.");
		}

		// Generate the Particle colors.

		$particleColors = "";
		foreach ($particleData->particles as $Y => $colors) {
			$particleColors .= "$colors,";
			$this->particles[$Y] = explode(",", $colors);
		}

		$particleColorsHEX = explode(",", rtrim(join(",", array_unique(explode(",", $particleColors))), ","));

		foreach ($particleColorsHEX as $particleHEX) {
			if ($particleHEX == "#0") continue;
			$particleColor = hextocolor::convert($particleHEX);
			$this->particlesColor[$particleHEX] = new DustParticle($particleColor);
		}

	}

	public function getPlayerWanted(): string {

		return $this->playerWanted;

	}

	public function run($entity) {

		for ($i = 1; $i <= 4; $i++) {
			$XAxis = 0.0;
			$YAxis = 0.0;
			for ($YAxisP = 0; $YAxisP <= 7; ++$YAxisP) {
				for ($XAxisP = 0; $XAxisP <= 7; ++$XAxisP) {
					$pos = new Vector3($entity->getPosition()->x, $entity->getPosition()->y, $entity->getPosition()->z);
					if ($i == 1) {
						$YO = $entity->getPosition()->y + 3;
						$XO = $entity->getPosition()->x + 0.7;
						$pos = new Vector3($XO - $XAxis, $YO - $YAxis, $entity->getPosition()->z - 1);
					}
					if ($i == 2) {
						$YO = $entity->getPosition()->y + 3;
						$XO = $entity->getPosition()->x + 0.7;
						$pos = new Vector3($XO - $XAxis, $YO - $YAxis, $entity->getPosition()->z + 1);
					}
					if ($i == 3) {
						$YO = $entity->getPosition()->y + 3;
						$XO = $entity->getPosition()->x + 1;
						$pos = new Vector3($XO, $YO - $YAxis, $entity->getPosition()->z + 0.7 - $XAxis);
					}
					if ($i == 4) {
						$YO = $entity->getPosition()->y + 3;
						$XO = $entity->getPosition()->x - 1;
						$pos = new Vector3($XO, $YO - $YAxis, $entity->getPosition()->z + 0.7 - $XAxis);
					}

					if ($this->particles[$YAxisP][$XAxisP] == "#0") continue;
					$entity->getWorld()->addParticle($pos, $this->particlesColor[$this->particles[$YAxisP][$XAxisP]]);

					$XAxis += 0.2;
				}
				$XAxis = 0.0;
				$YAxis += 0.2;
			}
		}

	}

}