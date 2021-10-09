<?php

namespace ErikPDev\AdvanceDeaths;
use ErikPDev\AdvanceDeaths\DeathTypes;
use ErikPDev\AdvanceDeaths\Main;
use ErikPDev\AdvanceDeaths\utils\DatabaseProvider;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
class DeathContainer {
    /** @var Main */
    private $plugin;
    /** @var Array */
    private $KeyWords;

    function __construct($plugin, $database) {$this->plugin = $plugin;$this->database = $database;$this->DeathTypes = new DeathTypes($this->plugin);}
    /**
	* Convert variables to proper Data
	*
	* @param \pocketmine\entity\Entity|Player $entity
    * @param string $keyWord
	*
	* @return string 
	*/
    function ExecuteHelper($entity, $keyWord, $derive){
        /** @param EntityDamageByEntityEvent $entity->GetLastDamageCause() */
        switch( strtolower($keyWord) ){
            case "{name}":
                if(!$entity instanceof Player) return $entity->getNameTag();
                return $entity->getName();
            case "{killer}":
                if($derive !== "death.attack.player" && $derive !== "death.attack.mob" && $derive !== "death.attack.arrow" && $derive !== "death.attack.explosion.player") return "?";
                if(!$entity->getLastDamageCause()->getDamager() instanceof Player) return $entity->getLastDamageCause()->getDamager()->getNameTag();
                return $entity->getLastDamageCause()->getDamager()->getName();
            case "{killercurrenthealth}":
                if($derive == "death.attack.player") return $entity->getLastDamageCause()->getDamager()->getMaxHealth();
                break;
                case "{killermaxhealth}":
                if($derive == "death.attack.player") return $entity->getLastDamageCause()->getDamager()->getMaxHealth();
                break;
            case "{weapon}":
                if($derive == "death.attack.player") return $entity->getLastDamageCause()->getDamager()->getInventory()->getItemInHand()->getName();
                break;
            default:
                return "?";
                break;
        }
        
    }
    /**
	* This will return the complete translation with KeyWords and proper formatting from the config.
	*
	* @param string $translate
    * @param \pocketmine\entity\Entity $entity
	*
	* @return string | NULL
	*/

    public function Translate($translate, $entity){
        $DeathMessage = $this->DeathTypes->DeathConverter($translate);
        preg_match_all("/{(\w+)}/", $DeathMessage, $KeyWordsFound);
        foreach ($KeyWordsFound[0] as $value => $KeyWord) {
            if($entity->getLastDamageCause() instanceof EntityDamageByEntityEvent){
                if($KeyWord == "{killer_kills}" || $KeyWord == "{killer_deaths}"  || $KeyWord == "{player_kills}" || $KeyWord == "{player_deaths}" || $KeyWord == "{player_kdr}" || $KeyWord == "{killer_kdr}"){continue;}      
            }
            $DeathMessage = str_replace($KeyWord, $this->ExecuteHelper($entity, $KeyWord, $translate->getText()), $DeathMessage);
        }
        
        preg_match_all("/{(\w+)}/", $DeathMessage, $RemaningMatches);
        if(count($RemaningMatches[0]) == 0) return $this->plugin->getServer()->broadcastMessage($DeathMessage);
        foreach ($RemaningMatches[0] as $value => $RemainingKeyWord){
            if($RemainingKeyWord == "{killer_kills}"){
                $this->database->getDatabase()->executeSelect(DatabaseProvider::GET_KILLS, ["UUID" => $entity->getLastDamageCause()->getDamager()->getUniqueID()->toString()], 
                function(array $rows) use (&$RemainingKeyWord, &$DeathMessage, &$RemaningMatches, $value){
                    $kills = $rows[0]["Kills"] ?? 0;
                    $DeathMessage = str_replace("{killer_kills}", (string)$kills, $DeathMessage);
                    if((count($RemaningMatches[0])-1) == $value) return $this->plugin->getServer()->broadcastMessage($DeathMessage);
                });
            }

            if($RemainingKeyWord == "{killer_deaths}"){
                $this->database->getDatabase()->executeSelect(DatabaseProvider::GET_DEATHS, ["UUID" => $entity->getLastDamageCause()->getDamager()->getUniqueID()->toString()], 
                function(array $rows) use (&$RemainingKeyWord, &$DeathMessage, &$RemaningMatches, $value){
                    $deaths = $rows[0]["Deaths"] ?? 0;
                    $DeathMessage = str_replace("{killer_deaths}", (string)$deaths, $DeathMessage);
                    if((count($RemaningMatches[0])-1) == $value) return $this->plugin->getServer()->broadcastMessage($DeathMessage);
                });
            }


            if($RemainingKeyWord == "{player_kills}"){
                $this->database->getDatabase()->executeSelect(DatabaseProvider::GET_KILLS, ["UUID" => $entity->getUniqueID()->toString()], 
                function(array $rows) use (&$RemainingKeyWord, &$DeathMessage, &$RemaningMatches, $value){
                    $kills = $rows[0]["Kills"] ?? 0;
                    $DeathMessage = str_replace("{player_kills}", (string)$kills, $DeathMessage);
                    if((count($RemaningMatches[0])-1) == $value) return $this->plugin->getServer()->broadcastMessage($DeathMessage);
                });
            }

            if($RemainingKeyWord == "{player_deaths}"){
                $this->database->getDatabase()->executeSelect(DatabaseProvider::GET_DEATHS, ["UUID" => $entity->getUniqueID()->toString()], 
                function(array $rows) use (&$RemainingKeyWord, &$DeathMessage, &$RemaningMatches, $value){
                    $deaths = $rows[0]["Deaths"] ?? 0;
                    $DeathMessage = str_replace("{player_deaths}", (string)$deaths, $DeathMessage);
                    if((count($RemaningMatches[0])-1) == $value) return $this->plugin->getServer()->broadcastMessage($DeathMessage);
                });
            }

            if($RemainingKeyWord == "{player_kdr}"){
                $this->database->getDatabase()->executeSelect(DatabaseProvider::GETKILLS_AND_DEATHS, ["UUID" => $entity->getUniqueID()->toString()], 
                function(array $rows) use (&$RemainingKeyWord, &$DeathMessage, &$RemaningMatches, $value){
                    $deaths = $rows[0]["Deaths"] ?? 0;
                    $kills = $rows[0]["Kills"] ?? 0;
                    
                    $DeathMessage = str_replace("{player_kdr}", (string)DatabaseProvider::getKillToDeathRatio($kills, $deaths), $DeathMessage);
                    if((count($RemaningMatches[0])-1) == $value) return $this->plugin->getServer()->broadcastMessage($DeathMessage);
                });
            }
            
            if($RemainingKeyWord == "{killer_kdr}"){
                $this->database->getDatabase()->executeSelect(DatabaseProvider::GETKILLS_AND_DEATHS, ["UUID" => $entity->getLastDamageCause()->getDamager()->getUniqueID()->toString()], 
                function(array $rows) use (&$RemainingKeyWord, &$DeathMessage, &$RemaningMatches, $value){
                    $deaths = $rows[0]["Deaths"] ?? 0;
                    $kills = $rows[0]["Kills"] ?? 0;
                    
                    $DeathMessage = str_replace("{killer_kdr}", (string)DatabaseProvider::getKillToDeathRatio($kills, $deaths), $DeathMessage);
                    if((count($RemaningMatches[0])-1) == $value) return $this->plugin->getServer()->broadcastMessage($DeathMessage);
                });
            }
        }
        
    }


}
