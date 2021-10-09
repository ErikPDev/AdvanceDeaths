<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\Player;
class instantRespawn implements Listener{


    public function __construct(){}

    /**
     * @priority HIGHEST
     * @ignoreCancelled false
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event){
        $pk = new \pocketmine\network\mcpe\protocol\GameRulesChangedPacket();
        $pk->gameRules = ["doimmediaterespawn" => [1, true, false]];
        $event->getPlayer()->sendDataPacket($pk);
    }
    
    /**
     * @priority HIGHEST
     * @ignoreCancelled false
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event){
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }
        
        $pk = new \pocketmine\network\mcpe\protocol\GameRulesChangedPacket();
        $pk->gameRules = ["doimmediaterespawn" => [1, true, false]];
        $player->sendDataPacket($pk);
    }

}