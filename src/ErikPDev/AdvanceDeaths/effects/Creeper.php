<?php

namespace ErikPDev\AdvanceDeaths\effects;
use pocketmine\world\particle\Particle;
use pocketmine\world\particle\DustParticle;
use pocketmine\color\Color;
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
              $YO = $player->getPosition()->y+3;
              $XO = $player->getPosition()->x+0.7;
              $pos = new Vector3($XO-$XAxis,$YO-$YAxis,$player->getPosition()->z-1);
            }
            if($i == 2){
              $YO = $player->getPosition()->y+3;
              $XO = $player->getPosition()->x+0.7;
              $pos = new Vector3($XO-$XAxis,$YO-$YAxis,$player->getPosition()->z+1);
            }
            if($i == 3){
              $YO = $player->getPosition()->y+3;
              $XO = $player->getPosition()->x+1;
              $pos = new Vector3($XO, $YO-$YAxis, $player->getPosition()->z+0.7-$XAxis);
            }
            if($i == 4){
              $YO = $player->getPosition()->y+3;
              $XO = $player->getPosition()->x-1;
              $pos = new Vector3($XO, $YO-$YAxis, $player->getPosition()->z+0.7-$XAxis);
            }


            $Creeper[0] = new DustParticle(new Color(76, 255, 0 ,255));
            
            $Creeper[1] = new DustParticle(new Color( 0, 0, 0, 0 ));
            // $Creeper[2] = new GenericParticle($pos, Particle::TYPE_DUST, ((255 & 0xff) << 24) | ((76 & 0xff) << 16) | ((179 & 0xff) << 8) | (65 & 0xff));
    
            // First Eye

            if($YAxisP == 3 && $XAxisP == 2){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 2){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 3 && $XAxisP == 3){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 3){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            // Second eye

            if($YAxisP == 3 && $XAxisP == 6){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 6){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 3 && $XAxisP == 7){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 4 && $XAxisP == 7){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            // Make the mouth

            if($YAxisP == 5 && $XAxisP == 4){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 5 && $XAxisP == 5){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 3){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 4){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 5){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 6 && $XAxisP == 6){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 7 && $XAxisP == 3){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 7 && $XAxisP == 4){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 7 && $XAxisP == 5){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 7 && $XAxisP == 6){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            if($YAxisP == 8 && $XAxisP == 3){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }
            
            if($YAxisP == 8 && $XAxisP == 6){
              $player->getWorld()->addParticle($pos, $Creeper[1]);
              $XAxis = $XAxis + 0.2;
              continue;
            }

            $player->getWorld()->addParticle($pos, $Creeper[0]);
            $XAxis = $XAxis + 0.2;
          }
          $XAxis = 0.0;
          $YAxis = $YAxis + 0.2;
        }
      }
    }
}