<?php
namespace ErikPDev\AdvanceDeaths\utils;

class configValidator{
    private $Config,$plugin,$VariablesInstances;
    public function __construct($plugin, $Config){
        $this->plugin = $plugin;
        $this->Config = $Config;
        $this->VariablesInstances = array(
            "Hitted-Hearts" => "bool",
            "Heal-Killer" => "bool",
            "HealMessage" => "string",
            "instant-respawn" => "bool",
            "DeathMoneyConfig" => "array",
            "KillMoneyConfig" => "array",
            "onDeathEffect" => "string",
            "NotOnWorlds" => "array",
            "FEnableFloatingText" => "bool",
            "FLeaderboardWorld" => "string",
            "FLeaderBoardCoordinates" => "array",
            "generic" => "string",
            "player" => "string",
            "mob" => "string",
            "outOfWorld" => "string",
            "suffocation" => "string",
            "onFire" => "string",
            "inFire" => "string",
            "drown" => "string",
            "explosion" => "string",
            "magic" => "string",
            "cactus" => "string",
            "highplace" => "string",
            "arrow" => "string",
            "lava" => "string",
            "explosion" => "string",
            "GenericExplosion" => "string",
            "database" => "array",
            "config-verison" => "float"
        );
        
    }

    private function isInstanceOf($value, $type){
        switch ($type) {
            case "string":
                if(is_string($value) == true){return true;}else{return false;}
                break;
            case 'bool':
                if(is_bool($value) == true){return true;}else{return false;}
                break;
            case 'array':
                if(is_array($value) == true){return true;}else{return false;}
                break;
            case 'float':
                if(is_float($value) == true){return true;}else{return false;}
                break;
            default:
                return false;
                break;
        }
    }
    public function Check(){
        $ConfigVariables = $this->Config->getAll();
        echo($this->Config->get("config-verison"));
        foreach ($ConfigVariables as $key => $value) {
            if(!array_key_exists($key, $this->VariablesInstances)) continue;
            if(!$this->isInstanceOf($value, $this->VariablesInstances[$key])){
                $this->plugin->getLogger()->critical("Unsupported value type on config.yml.");
                $this->plugin->getLogger()->critical("Key: $key, ".$this->VariablesInstances[$key]);
                $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
            }
        }
    }
}