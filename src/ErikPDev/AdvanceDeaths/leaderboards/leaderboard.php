<?php

namespace ErikPDev\AdvanceDeaths\leaderboards;

use ErikPDev\AdvanceDeaths\ADMain;
use ErikPDev\AdvanceDeaths\leaderboards\events\leaderboardClose;
use ErikPDev\AdvanceDeaths\leaderboards\events\leaderboardDataUpdate;
use ErrorException;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;

class leaderboard implements Listener {


	private Vector3 $vectorPos;

	private string $worldName;

	private FloatingTextParticle $leaderboard;

	public function __construct(public array $configuration, private int $type) {

		$pos = [];

		foreach (explode(",", $this->configuration["coordinates"]) as $value) {
			if (!preg_match("/^\d+$/", str_replace("-", "", $value))) {
				throw new ErrorException("Coordinates is not an integer in leaderboards.yml");
			}
			$pos[] = intval($value);
		}

		$this->vectorPos = new Vector3($pos[0], $pos[1], $pos[2]);
		$this->worldName = $this->configuration["worldName"];

		if (Server::getInstance()->getWorldManager()->getWorldByName($this->worldName) == null) {
			ADMain::getInstance()->getLogger()->critical("Please double check your world name.");
			Server::getInstance()->getPluginManager()->disablePlugin(ADMain::getInstance());
			return;
		}

		if (!Server::getInstance()->getWorldManager()->isWorldLoaded($this->worldName)) {
			Server::getInstance()->getWorldManager()->loadWorld($this->worldName);
		}

		if (!Server::getInstance()->getWorldManager()->getWorldByName($this->worldName)->isChunkLoaded($pos[0] >> 4, $pos[2] >> 4)) {
			Server::getInstance()->getWorldManager()->getWorldByName($this->worldName)->loadChunk($pos[0] >> 4, $pos[2] >> 4);
		}

		$this->leaderboard = new FloatingTextParticle("Loading...", $this->configuration["title"]);
		$this->leaderboard->setInvisible(false);

	}

	private function addParticle() {

		Server::getInstance()->getWorldManager()->getWorldByName($this->worldName)->addParticle($this->vectorPos, $this->leaderboard);

	}

	public function onDisabled(leaderboardClose $event){

		$this->leaderboard->setInvisible();

	}

	public function dataUpdate(leaderboardDataUpdate $event) {

		$typeName = match ($this->type) {
			0 => "Kills",
			1 => "Deaths",
			2 => "Killstreak",
			default => "?",
		};

		$data = $event->getData()[$typeName];
		$text = "";

		foreach ($data as $place => $variables) {

			$text .= ($place+1).". ".$variables["PlayerName"]." - ".$variables[$typeName]." $typeName\n";

		}

		$this->leaderboard->setText($text);

	}

	public function onJoin(PlayerJoinEvent $event) {
        if ($event->getPlayer()->getWorld()->getDisplayName())
		$this->addParticle();

	}

    public function onWorldChange(EntityTeleportEvent $event){
        $oldWorld = $event->getFrom()->getWorld()->getDisplayName();
        $newWorld = $event->getTo()->getWorld()->getDisplayName();
        if($oldWorld === $newWorld) return;
        $this->addParticle();
    }

	public function onDeath(PlayerDeathEvent $event) {

		$this->addParticle();

	}

}