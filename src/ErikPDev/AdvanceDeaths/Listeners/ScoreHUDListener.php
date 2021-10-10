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
    
        if($tag->getName() == "advancedeaths.myDeaths" || $tag->getName() == "advancedeaths.myKills" || $tag->getName() == "advancedeaths.topKiller" || $tag->getName() == "advancedeaths.kdr"){
            $tag->setValue("?");
        }
        switch ($tag->getname()) {
            case "advancedeaths.myDeaths":
                $this->Database->getDatabase()->executeSelect(DatabaseProvider::GET_DEATHS, ["UUID" => $player->getUniqueID()->toString()], 
                    function(array $rows) use (&$player){
                        $deaths = $rows[0]["Deaths"] ?? 0;
                        $DeathsEv = new PlayerTagUpdateEvent($player, new ScoreTag("advancedeaths.myDeaths",strval($deaths)));
                        $DeathsEv->call();
                    });
                break;
            case "advancedeaths.myKills":
                $this->Database->getDatabase()->executeSelect(DatabaseProvider::GET_KILLS, ["UUID" => $player->getUniqueID()->toString()], 
                    function(array $rows) use (&$player){
                        $kills = $rows[0]["Kills"] ?? 0;
                        $KillsEv = new PlayerTagUpdateEvent($player, new ScoreTag("advancedeaths.myKills",strval($kills)));
                        $KillsEv->call();
                    });
                break;
            case "advancedeaths.topKiller":
                $this->Database->getDatabase()->executeSelect(DatabaseProvider::SCOREBOARD_TOP,[], 
                    function(array $rows){
                        $PlayerName = $rows[0]["PlayerName"] ?? "?";
                        $kills = $rows[0]["Kills"] ?? 0;
                        $topKiller = new ServerTagUpdateEvent(new ScoreTag("advancedeaths.topKiller",$PlayerName.":".strval($kills)));
                        $topKiller->call();
                    });
                break;
            case "advancedeaths.kdr":
                $this->Database->getDatabase()->executeSelect(DatabaseProvider::GETKILLS_AND_DEATHS, ["UUID" => $event->getPlayer()->getUniqueID()->toString()], 
                    function(array $rows) use (&$player){
                        $deaths = $rows[0]["Deaths"] ?? 0;
                        $kills = $rows[0]["Kills"] ?? 0;
                        $kdr = new PlayerTagUpdateEvent($player, new ScoreTag("advancedeaths.kdr",strval(DatabaseProvider::getKillToDeathRatio($kills, $deaths))));
                        $kdr->call();
                    });
                break;
            default:
                break;
        }
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


        $this->Database->getDatabase()->executeSelect(DatabaseProvider::GETKILLS_AND_DEATHS, ["UUID" => $player->getUniqueID()->toString()], 
            function(array $rows) use(&$player){
                $deaths = $rows[0]["Deaths"] ?? 0;
                $kills = $rows[0]["Kills"] ?? 0;
                $kdr = new PlayerTagUpdateEvent($player, new ScoreTag("advancedeaths.kdr",strval(DatabaseProvider::getKillToDeathRatio($kills, $deaths))));
                $kdr->call();
            });

        $this->Database->getDatabase()->executeSelect(DatabaseProvider::GETKILLS_AND_DEATHS, ["UUID" => $damager->getUniqueID()->toString()], 
            function(array $rows) use(&$damager){
                $deaths = $rows[0]["Deaths"] ?? 0;
                $kills = $rows[0]["Kills"] ?? 0;
                $kdr = new PlayerTagUpdateEvent($damager, new ScoreTag("advancedeaths.kdr",strval(DatabaseProvider::getKillToDeathRatio($kills, $deaths))));
                $kdr->call();
            });
        
    }
}