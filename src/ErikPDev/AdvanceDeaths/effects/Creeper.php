<?php

namespace ErikPDev\AdvanceDeaths\effects;
use pocketmine\level\particle\Particle;
use pocketmine\level\particle\GenericParticle;
use pocketmine\math\Vector3;
class Creeper{
    private $player;
    function __construct($player){
        $this->player = $player;
    }

    public function run(){
      $player = $this->player;
      for ($i=1; $i <= 4; $i++) { 
        $XAxis = 0.0;
        $YAxis = 0.0;
        for($YAxisP = 1; $YAxisP <= 8; ++$YAxisP){
          for($XAxisP = 1; $XAxisP <= 8; ++$XAxisP){
            if($i == 1){
              $YO = $player->asVector3()->getY()+3;
              $XO = $player->asVector3()->getX()+0.7;
              $pos = new Vector3($XO-$XAxis,$YO-$YAxis,$player->asVector3()->getZ()-1);
            }
            if($i == 2){
              $YO = $player->asVector3()->getY()+3;
              $XO = $player->asVector3()->getX()+0.7;
              $pos = new Vector3($XO-$XAxis,$YO-$YAxis,$player->asVector3()->getZ()+1);
            }
            if($i == 3){
              $YO = $player->asVector3()->getY()+3;
              $XO = $player->asVector3()->getX()+1;
              $pos = new Vector3($XO, $YO-$YAxis, $player->asVector3()->getZ()+0.7-$XAxis);
            }
            if($i == 4){
              $YO = $player->asVector3()->getY()+3;
              $XO = $player->asVector3()->getX()-1;
              $pos = new Vector3($XO, $YO-$YAxis, $player->asVector3()->getZ()+0.7-$XAxis);
            }


            $Creeper[0] = new GenericParticle($pos, Particle::TYPE_DUST, ((255 & 0xff) << 24) | ((85 & 0xff) << 16) | ((255 & 0xff) << 8) | (85 & 0xff));
            
            $Creeper[1] = new GenericParticle($pos, Particle::TYPE_DUST, ((255 & 0xff) << 24) | ((0 & 0xff) << 16) | ((0 & 0xff) << 8) | (0 & 0xff));
            // $Creeper[2] = new GenericParticle($pos, Particle::TYPE_DUST, ((255 & 0xff) << 24) | ((76 & 0xff) << 16) | ((179 & 0xff) << 8) | (65 & 0xff));
    
            // First Eye

            if($YAxisP == 3 && $XAxisP == 2){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 2){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 3 && $XAxisP == 3){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 3){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            // Second eye

            if($YAxisP == 3 && $XAxisP == 6){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 6){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 3 && $XAxisP == 7){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 7){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            // Make the mouth

            if($YAxisP == 5 && $XAxisP == 4){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 5 && $XAxisP == 5){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 3){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 4){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 5){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 6){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 7 && $XAxisP == 3){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 7 && $XAxisP == 4){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 7 && $XAxisP == 5){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 7 && $XAxisP == 6){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 8 && $XAxisP == 3){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 8 && $XAxisP == 6){
              $player->getLevel()->addParticle($Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            $player->getLevel()->addParticle($Creeper[0]);
            $XAxis = $XAxis + 0.2;
          }
          $XAxis = 0.0;
          $YAxis = $YAxis + 0.2;
        }
      }
    }
}