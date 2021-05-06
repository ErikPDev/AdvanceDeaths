<?php

namespace ErikX\AdvanceDeaths;
use ErikX\AdvanceDeaths\DeathTypes;
class DeathContainer {
    private $plugin;
    private $translate;
    private $DeathTypes;
    private $KeyWords;
    function __construct($plugin) {
        $this->plugin = $plugin;
        $this->KeyWords = array(
            "{name}" => '$entity->getName',
            "{killer}" => '',
            "{killerCurrentHealth}" => '$entity->getLastDamageCause()->getDamager()->getHealth',
            "{killerMaxHealth}" => '$entity->getLastDamageCause()->getDamager()->getMaxHealth',
            "{weapon}" => '$entity->getLastDamageCause()->getDamager()->getInventory()->getItemInHand()->getName',
        );

    }
    function ExecuteHelper($entity, $keyWord){
        switch( strtolower($keyWord) ){
            case "{name}":
                return $entity->getName();
            case "{killer}":
                return $entity->getLastDamageCause()->getDamager()->getName();
            case "{killercurrenthealth}":
                return $entity->getLastDamageCause()->getDamager()->getMaxHealth();
            case "{killermaxhealth}":
                return $entity->getLastDamageCause()->getDamager()->getMaxHealth();
            case "{weapon}":
                return $entity->getLastDamageCause()->getDamager()->getInventory()->getItemInHand()->getName();
        }
    }
    public function Translate($translate, $entity){
        

        $DeathTypes = new DeathTypes($this->plugin);
        $DeathMessage = $DeathTypes->DeathConverter($translate);
        $PlayerName = $entity->getName();
        foreach($this->KeyWords as $keyWord => $variable){
            if ( strpos( strtolower($DeathMessage) , strtolower($keyWord) ) !== false ){
                $DeathMessage = str_replace( $keyWord, $this->ExecuteHelper($entity, $keyWord) , $DeathMessage );
            }
        }
        
        return $DeathMessage;
    }


}
