<?php

namespace ErikPDev\AdvanceDeaths;
use ErikPDev\AdvanceDeaths\Main;
class DeathTypes {

    /** @var Main */
    private $plugin;
    function __construct($plugin) {
        $this->plugin = $plugin;
    }
    /**
	* Convert message to config Translation
	*
	* @param string $id
	*
	* @return string | NULL
	*/
    public function DeathConverter($death){
        
        $array = array(
            "death.attack.generic" => "generic",
            "death.attack.player" => "player",
            "death.attack.mob" => "mob",
            "death.attack.outOfWorld" => "outOfWorld",
            "death.attack.inWall" => "suffocation",
            "death.attack.onFire" => "onFire",
            "death.attack.inFire" => "inFire",
            "death.attack.drown" => "drown",
            "death.attack.magic" => "magic",
            "death.attack.cactus" => "cactus",
            "death.fell.accident.generic" => "highplace",
            "death.attack.arrow" => "arrow",
            "death.attack.lava" => "lava",
            "death.attack.explosion.player" => "explosion",
            "death.attack.fall" => "highplace",
            "death.attack.explosion" => "GenericExplosion"
        );
        if(!array_key_exists($death->getText(), $array)){
            $this->plugin->getLogger()->critical("Unknown Death Derive! Create an issue at github for AdvanceDeats with the text below.");
            $this->plugin->getLogger()->critical("Error Code: 102");
            $this->plugin->getLogger()->critical("Unknown Derive: ".(string)$death->getText());
            return 102;
        }
        $deathSTRARRY = $array[$death->getText()]; // Get the translated death
        $DeathMessage = $this->plugin->getConfig()->get($deathSTRARRY); // Get the death message from config
        return $DeathMessage; // Return the Death
    }
    
   

}
