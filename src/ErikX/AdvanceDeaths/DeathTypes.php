<?php

namespace ErikX\AdvanceDeaths;

class DeathTypes {
    private $plugin;
    function __construct($plugin) {
        $this->plugin = $plugin;
    }
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
        );
        $deathSTR = (string) $death; // Turn $death to string
        $deathSTRARRY = $array[$deathSTR]; // Get the translated death
        $DeathMessage = $this->plugin->getConfig()->get($deathSTRARRY); // Get the death message from config
        return $DeathMessage; // Return the Death
    }
    
   

}
