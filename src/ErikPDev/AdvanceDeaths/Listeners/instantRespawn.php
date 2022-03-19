<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\player\Player;
class instantRespawn implements Listener{


    public function __construct(){}

    /**
     * @priority HIGHEST
     * @ignoreCancelled false
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event){
        $pk = new GameRulesChangedPacket::create(
            ["doimmediaterespawn" => new BoolGameRule(true, false)]
        );
        $event->getPlayer()->getNetworkSession()->sendDataPacket($pk);
    }
    
    /**
     * @priority HIGHEST
     * @ignoreCancelled false
     * @param EntityTeleportEvent $event
     */
    public function onLevelChange(EntityTeleportEvent $event){
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }
        
        $pk = new GameRulesChangedPacket::create(
            ["doimmediaterespawn" => new BoolGameRule(true, false)]
        );

        $player->getNetworkSession()->sendDataPacket($pk);
    }

}
