<?php
namespace ErikPDev\AdvanceDeaths\utils;
use pocketmine\event\Listener;
use ErikPDev\AdvanceDeaths\utils\DatabaseProvider;
use pocketmine\event\player\PlayerDeathEvent;

class leaderboardData implements Listener{
    private $db;
    private static $killsLeaderboard, $deathsLeaderboard, $killstreaksLeaderboard = "";
    public function __construct($db){
        $this->db = $db;
        self::$killsLeaderboard = "";
        self::$deathsLeaderboard = "";
        self::$killstreaksLeaderboard = "";
        $this->updateData();
    }

    private function updateData(){
        $this->db->getDatabase()->executeSelect(DatabaseProvider::TOP5KILLS,[], 
            function(array $rows){
                self::$killsLeaderboard = "";
                foreach ($rows as $X => $Element) {
                    self::$killsLeaderboard .= strval($X+1).". ".$Element["PlayerName"]." - Kills: ".strval($Element["Kills"])."\n";
                }
            });
            
        $this->db->getDatabase()->executeSelect(DatabaseProvider::TOP5DEATHS,[], 
            function(array $rows){
                self::$deathsLeaderboard = "";
                foreach ($rows as $X => $Element) {
                    self::$deathsLeaderboard .= strval($X+1).". ".$Element["PlayerName"]." - Deaths: ".strval($Element["Deaths"])."\n";
                }
            });
        $this->db->getDatabase()->executeSelect(DatabaseProvider::TOP5KillSTREAKS,[], 
            function(array $rows){
                self::$killstreaksLeaderboard = "";
                foreach ($rows as $X => $Element) {
                    self::$killstreaksLeaderboard .= strval($X+1).". ".$Element["PlayerName"]." - Killstreak: ".strval($Element["Killstreak"])."\n";
                }
            });

    }

    public function onDeath(PlayerDeathEvent $event){
        $this->updateData();
    }

    public static function getKillsLeaderboard(){
        return self::$killsLeaderboard;
    }

    public static function getDeathsLeaderboard(){
        return self::$deathsLeaderboard;
    }

    public static function getKillstreaksLeaderboard(){
        return self::$killstreaksLeaderboard;
    }
}