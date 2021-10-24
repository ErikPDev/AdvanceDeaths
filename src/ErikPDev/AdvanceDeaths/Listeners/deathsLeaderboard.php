<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use ErikPDev\AdvanceDeaths\utils\leaderboardData;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;

class deathsLeaderboard implements Listener{
    private $world;
    private $deathsLeaderboard;
    public function __construct($plugin){
        $pos = $plugin->getConfig()->get("DeathsFLeaderBoardCoordinates");
        $this->world = $plugin->getConfig()->get("DeathsFLeaderboardWorld");
        if(!Server::getInstance()->isLevelLoaded($this->world)) {
          Server::getInstance()->loadLevel($this->world);
        }
        if(!Server::getInstance()->getLevelByName($this->world)->isChunkLoaded($pos["X"] >> 4, $pos["Z"] >> 4)) {
          Server::getInstance()->getLevelByName($this->world)->loadChunk($pos["X"] >> 4, $pos["Z"] >> 4);
        }
        
        $this->deathsLeaderboard = new FloatingTextParticle(new Vector3($pos["X"],$pos["Y"],$pos["Z"]), "Loading...", "§bAdvance§cDeaths§6 Deaths");
        $this->deathsLeaderboard->setInvisible(false);
        $this->updateLeaderboard();
    }

    private function updateLeaderboard(){
        $this->deathsLeaderboard->setText(leaderboardData::getDeathsLeaderboard());
        Server::getInstance()->getLevelByName($this->world)->addParticle($this->deathsLeaderboard);
    }

    public function disableLeaderboard(){
        if(isset($this->deathsLeaderboard)) $this->deathsLeaderboard->setInvisible(true);
    }

    public function onJoin(PlayerJoinEvent $event){
        Server::getInstance()->getLevelByName($this->world)->addParticle($this->deathsLeaderboard, [$event->getPlayer()]);
    }

    public function onDeath(PlayerDeathEvent $event){
        $this->updateLeaderboard();
    }
}