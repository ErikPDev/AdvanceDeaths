<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use ErikPDev\AdvanceDeaths\utils\DatabaseProvider;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
class ScoreHUDListener implements Listener{

    /** @var DatabaseProvider */
    private $Database;


    public function __construct(DatabaseProvider $Database)
    {
        $this->Database = $Database;
    }

    public function onTagResolve(TagsResolveEvent $event){
        $player = $event->getPlayer();
        $tag = $event->getTag();
    
        if($tag->getName() == "advancedeaths.myDeaths" || $tag->getName() == "advancedeaths.myKills" || $tag->getName() == "advancedeaths.topKiller"){
            $tag->setValue("?");
        }
    }
    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->Database->getDatabase()->executeSelect(DatabaseProvider::GETKILLS_AND_DEATHS, ["UUID" => $event->getPlayer()->getUniqueID()->toString()], 
            function(array $rows) use (&$player){
                $deaths = $rows[0]["Deaths"] ?? 0;
                $kills = $rows[0]["Kills"] ?? 0;
                $DeathsEv = new PlayerTagUpdateEvent($player, new ScoreTag("advancedeaths.myDeaths",strval($deaths)));
                $DeathsEv->call();
                $KillsEv = new PlayerTagUpdateEvent($player, new ScoreTag("advancedeaths.myKills",strval($kills)));
                $KillsEv->call();
            });

            $this->Database->getDatabase()->executeSelect(DatabaseProvider::SCOREBOARD_TOP,[], 
            function(array $rows){
                $PlayerName = $rows[0]["PlayerName"] ?? "?";
                $kills = $rows[0]["Kills"] ?? 0;
                $topKiller = new ServerTagUpdateEvent(new ScoreTag("advancedeaths.topKiller",$PlayerName.":".strval($kills)));
                $topKiller->call();
            });
    }

    public function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if(!$player->getLastDamageCause() instanceof EntityDamageByEntityEvent) return;
        $damager = $player->getLastDamageCause()->getDamager();
        if(!$damager instanceof Player) return;
        
        $this->Database->getDatabase()->executeSelect(DatabaseProvider::GET_DEATHS, ["UUID" => $player->getUniqueID()->toString()], 
            function(array $rows) use (&$player){
                $deaths = $rows[0]["Deaths"] ?? 0;
                $DeathsEv = new PlayerTagUpdateEvent($player, new ScoreTag("advancedeaths.myDeaths",strval($deaths)));
                $DeathsEv->call();
            });

        $this->Database->getDatabase()->executeSelect(DatabaseProvider::GET_KILLS, ["UUID" => $damager->getUniqueID()->toString()], 
            function(array $rows) use (&$damager){
                $kills = $rows[0]["Kills"] ?? 0;
                $KillsEv = new PlayerTagUpdateEvent($damager, new ScoreTag("advancedeaths.myKills",strval($kills)));
                $KillsEv->call();
            });

        $this->Database->getDatabase()->executeSelect(DatabaseProvider::SCOREBOARD_TOP,[], 
            function(array $rows){
                $PlayerName = $rows[0]["PlayerName"] ?? "?";
                $kills = $rows[0]["Kills"] ?? 0;
                $topKiller = new ServerTagUpdateEvent(new ScoreTag("advancedeaths.topKiller",$PlayerName.":".strval($kills)));
                $topKiller->call();
            });
        
    }
}