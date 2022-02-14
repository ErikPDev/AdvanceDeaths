<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use ErikPDev\AdvanceDeaths\utils\leaderboardData;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;

class killsLeaderboard implements Listener{
    /**
     * @var string $world
     * @var Vector3 $vector3pos
     */
    private $world,$vector3pos;
    /**
     * @var FloatingTextParticle $KillstreakLeaderBoard
     */
    private $KillstreakLeaderBoard;
    public function __construct($plugin){
        $pos = $plugin->getConfig()->get("KillsFLeaderBoardCoordinates");
        $this->vector3pos = new Vector3($pos["X"],$pos["Y"],$pos["Z"]);
        $this->world = $plugin->getConfig()->get("KillsFLeaderboardWorld");
        if(!Server::getInstance()->getWorldManager()->isWorldLoaded($this->world)) {
          Server::getInstance()->getWorldManager()->loadWorld($this->world);
        }
        if(!Server::getInstance()->getWorldManager()->getWorldByName($this->world)->isChunkLoaded($pos["X"] >> 4, $pos["Z"] >> 4)) {
          Server::getInstance()->getWorldManager()->getWorldByName($this->world)->loadChunk($pos["X"] >> 4, $pos["Z"] >> 4);
        }
        
        $this->KillsLeaderBoard = new FloatingTextParticle("Loading...", "§bAdvance§cDeaths§6 Kills");
        $this->KillsLeaderBoard->setInvisible(false);
        $this->updateLeaderboard();
    }

    private function updateLeaderboard(){
        $this->KillsLeaderBoard->setText(leaderboardData::getKillsLeaderboard());
        Server::getInstance()->getWorldManager()->getWorldByName($this->world)->addParticle($this->vector3pos, $this->KillsLeaderBoard);
    }

    public function disableLeaderboard(){
        if(isset($this->KillsLeaderBoard)) $this->KillsLeaderBoard->setInvisible(true);
    }

    public function onJoin(PlayerJoinEvent $event){
        Server::getInstance()->getWorldManager()->getWorldByName($this->world)->addParticle($this->vector3pos, $this->KillsLeaderBoard, [$event->getPlayer()]);
    }

    public function onDeath(PlayerDeathEvent $event){
        $this->updateLeaderboard();
    }
}
