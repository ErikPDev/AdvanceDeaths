<?php

namespace ErikPDev\AdvanceDeaths\effects;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;

class Lighting{
    private $player;
    function __construct($player){
        $this->player = $player;
    }
    
    public function run(){
        $player = $this->player;
        $location = $player->getLocation();
        $id = Entity::nextRuntimeId();
        $light = AddActorPacket::create(
            $id, //Actor unique ID not implemented
            $id,
            "minecraft:lightning_bolt",
            $location->asVector3(),
            null,
            $location->pitch,
            $location->yaw,
            $location->yaw, //Head yaw not implemented
            [],
            [],
            []
        );
        $sound = PlaySoundPacket::create(
            "ambient.weather.thunder",
            $location->x,
            $location->y,
            $location->z,
            1,
            1
        );
        Server::getInstance()->broadcastPackets($player->getWorld()->getPlayers(), [$light, $sound]);
    }
}
