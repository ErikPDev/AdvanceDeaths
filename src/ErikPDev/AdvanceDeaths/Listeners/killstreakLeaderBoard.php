<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use ErikPDev\AdvanceDeaths\utils\leaderboardData;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;

class killstreakLeaderBoard implements Listener{
    private $world;
    private $KillstreakLeaderBoard;
    public function __construct($plugin){
        $pos = $plugin->getConfig()->get("KillstreaksFLeaderBoardCoordinates");
        $this->world = $plugin->getConfig()->get("KillstreaksFLeaderboardWorld");
        if(!Server::getInstance()->getWorldManager()->isWorldLoaded($this->world)) {
          Server::getInstance()->getWorldManager()->loadWorld($this->world);
        }
        if(!Server::getInstance()->getWorldManager()->getWorldByName($this->world)->isChunkLoaded($pos["X"] >> 4, $pos["Z"] >> 4)) {
          Server::getInstance()->getWorldManager()->getWorldByName($this->world)->loadChunk($pos["X"] >> 4, $pos["Z"] >> 4);
        }
        
        $this->KillstreakLeaderBoard = new FloatingTextParticle(new Vector3($pos["X"],$pos["Y"],$pos["Z"]), "Loading...", "§bAdvance§cDeaths§6 Killstreak");
        $this->KillstreakLeaderBoard->setInvisible(false);
        $this->updateLeaderboard();
    }

    private function updateLeaderboard(){
        $this->KillstreakLeaderBoard->setText(leaderboardData::getKillstreaksLeaderboard());
        Server::getInstance()->getWorldManager()->getWorldByName($this->world)->addParticle($this->KillstreakLeaderBoard);
    }

    public function disableLeaderboard(){
        if(isset($this->KillstreakLeaderBoard)) $this->KillstreakLeaderBoard->setInvisible(true);
    }

    public function onJoin(PlayerJoinEvent $event){
        Server::getInstance()->getWorldManager()->getWorldByName($this->world)->addParticle($this->KillstreakLeaderBoard, [$event->getPlayer()]);
    }

    public function onDeath(PlayerDeathEvent $event){
        $this->updateLeaderboard();
    }
}
