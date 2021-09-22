<?php
namespace ErikPDev\AdvanceDeaths\utils;
use poggit\libasynql\libasynql;
use ErikPDev\AdvanceDeaths\Main;

class DatabaseProvider{
    
    public $database;
    public const PREPARE_DATABASE = "advancedeaths.init";
    public const INCREASEMENT_KILL = "advancedeaths.addKill";
    public const INCREASEMENT_DEATH = "advancedeaths.addDeath";
    public const GET_KILLS = "advancedeaths.getKills";
    public const GET_DEATHS = "advancedeaths.getDeaths";
    public const GETKILLS_AND_DEATHS = "advancedeaths.getKills&Deaths";
    public const SCOREBOARD_TOP = "advancedeaths.ScoreBoardTOP";
    public const SCOREBOARD_TOP5 = "advancedeaths.ScoreBoardTOP5";
    /** @var Main */
    private $plugin;

    function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function prepare(): void{
        $this->database = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);
        $this->database->executeGeneric(DatabaseProvider::PREPARE_DATABASE, []);
	}

    public function IncrecementKill(string $UUID, string $PlayerName): void{
        $this->database->executeInsert(DatabaseProvider::INCREASEMENT_KILL, ["UUID" => $UUID, "PlayerName" => $PlayerName]);
    }

    public function IncrecementDeath(string $UUID, string $PlayerName): void{
        $this->database->executeInsert(DatabaseProvider::INCREASEMENT_DEATH, ["UUID" => $UUID, "PlayerName" => $PlayerName]);
    }

    public function getDatabase(){
        return $this->database;
    }

    public static function getKillToDeathRatio($kills, $deaths): string{
		if($deaths !== 0){
			$ratio = $kills / $deaths;
			if($ratio !== 0){
				return number_format($ratio, 1);
			}
		}
		return "0.0";
	}

    public function close(): void{
        if(isset($this->database)) $this->database->close();
    }
}