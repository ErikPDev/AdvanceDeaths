<?php

namespace ErikX\AdvanceDeaths;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Human;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\utils\Random;
use pocketmine\level\Level;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;
use ErikX\AdvanceDeaths\DeathContainer;
use ErikX\AdvanceDeaths\Update;

class Main extends PluginBase implements Listener {
  private $DeathContainer;
  public function onEnable() {
      $this->getServer()->getPluginManager()->registerEvents($this,$this);
      $this->saveDefaultConfig();
      $this->reloadConfig();
      if ($this->getConfig()->get("config-verison") != 1){
        $this->getLogger()->critical("Your config.yml file for AdvanceDeaths is outdated. Please use the new config.yml. To get it, delete the the old one.");
        $this->getServer()->getPluginManager()->disablePlugin($this);
      }
        
      $this->DeathContainer = new DeathContainer($this);
      Server::getInstance()->getAsyncPool()->submitTask(new Update("AdvanceDeaths", "1.8"));
    }
    public function onLoad(){
      $this->reloadConfig();
    }
    public function onDeath(PlayerDeathEvent $event){
      $player = $event->getPlayer();
      $name = $player->getName();
      $entity = $event->getEntity();
      $msgderive = $event->deriveMessage($entity->getDisplayName(), $entity->getLastDamageCause());
      $event->setDeathMessage($this->DeathContainer->Translate($msgderive, $entity)); // Changed the Broadcast message to SetDeathMessage

      if($event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent){

        if($this->getConfig()->get("Heal-Killer") == true and $entity->getLastDamageCause()->getDamager() instanceof Player and $msgderive == "death.attack.player"){
          $entity->getLastDamageCause()->getDamager()->setHealth($player->getMaxHealth());
          $entity->getLastDamageCause()->getDamager()->setFood($player->getMaxFood());
          $entity->getLastDamageCause()->getDamager()->sendMessage($this->getConfig()->get("HealMessage"));
          
        }
      }
      
    }

    public function onDamage(EntityDamageEvent $event) {
      $player = $event->getEntity();
      $entity = $event->getEntity();
      if($player->isCreative()){return;}
      if ($player instanceof Player && $this->getConfig()->get("Hitted-Hearts") == true && $event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent){
        $xd = (float) 1;
        $yd = (float) 1;
        $zd = (float) 1;
        $level = $player->getServer()->getDefaultLevel();
        $pos = $player->getPosition();

        $count = 1; // Is this too much?

        $data = null;

        $particle = new HeartParticle($pos, 0);

        $random = new Random((int) (microtime(true) * 1000) + mt_rand());

        for($i = 0; $i < $count; ++$i){
          $particle->setComponents($pos->x, $pos->y+0.5, $pos->z);
          $level->addParticle($particle);
        }
      }

      if($this->getConfig()->get("immediate-respawn") == true){

        if($event->getFinalDamage() >= $player->getHealth()) {
          if($player instanceof Player){
            $event->setCancelled();
    
            $player->setHealth($player->getMaxHealth());
            $player->setFood($player->getMaxFood());
            $player->addTitle($this->getConfig()->get("TitleDied"), $this->getConfig()->get("SubTitleDied"), 1, 100, 50);
            $name = $player->getName();
            if($this->getConfig()->get("keepInventory") == false){
              $inventory = $player->getInventory();
              $pos = $player->getPosition();
              $level = $player->getLevel();
              $AmrorInventory = $player->getArmorInventory();
              $inventory->dropContents($level,$pos);
              $AmrorInventory->dropContents($level,$pos);

            }
            $inventory = $player->getInventory();
            $AttemptonDeath = new PlayerDeathEvent($player, $inventory->getContents(), null, $player->getXpDropAmount());
            $AttemptonDeath->call();

            $xd = (float) 1;
            $yd = (float) 1;
            $zd = (float) 1;
            $level = $player->getServer()->getDefaultLevel();
			      $pos = $player->getPosition();

            $count = 50; // Is this too much?

            $data = null;

            $particle = new ExplodeParticle($pos);

            $random = new Random((int) (microtime(true) * 1000) + mt_rand());

            for($i = 0; $i < $count; ++$i){
              $particle->setComponents($pos->x + $random->nextSignedFloat() * $xd,$pos->y + $random->nextSignedFloat() * $yd, $pos->z + $random->nextSignedFloat() * $zd);
              $level->addParticle($particle);
            }
            $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
          
          }
        }
      }
    
    }



}
