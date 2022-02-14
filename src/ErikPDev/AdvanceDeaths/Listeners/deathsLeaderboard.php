<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use ErikPDev\AdvanceDeaths\utils\leaderboardData;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;

class deathsLeaderboard implements Listener{
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
        $pos = $plugin->getConfig()->get("DeathsFLeaderBoardCoordinates");
        $this->vector3pos = new Vector3($pos["X"],$pos["Y"],$pos["Z"]);
        $this->world = $plugin->getConfig()->get("DeathsFLeaderboardWorld");
        if(!Server::getInstance()->getWorldManager()->isWorldLoaded($this->world)) {
          Server::getInstance()->getWorldManager()->loadWorld($this->world);
        }
        if(!Server::getInstance()->getWorldManager()->getWorldByName($this->world)->isChunkLoaded($pos["X"] >> 4, $pos["Z"] >> 4)) {
          Server::getInstance()->getWorldManager()->getWorldByName($this->world)->loadChunk($pos["X"] >> 4, $pos["Z"] >> 4);
        }
        
        $this->deathsLeaderboard = new FloatingTextParticle("Loading...", "§bAdvance§cDeaths§6 Deaths");
        $this->deathsLeaderboard->setInvisible(false);
        $this->updateLeaderboard();
    }

    private function updateLeaderboard(){
        $this->deathsLeaderboard->setText(leaderboardData::getDeathsLeaderboard());
        Server::getInstance()->getWorldManager()->getWorldByName($this->world)->addParticle($this->vector3pos,$this->deathsLeaderboard);
    }

    public function disableLeaderboard(){
        if(isset($this->deathsLeaderboard)) $this->deathsLeaderboard->setInvisible(true);
    }

    public function onJoin(PlayerJoinEvent $event){
        Server::getInstance()->getWorldManager()->getWorldByName($this->world)->addParticle($this->vector3pos, $this->deathsLeaderboard, [$event->getPlayer()]);
    }

    public function onDeath(PlayerDeathEvent $event){
        $this->updateLeaderboard();
    }
}
