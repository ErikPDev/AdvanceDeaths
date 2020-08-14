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
class Main extends PluginBase implements Listener { //Added "implements Listener" because of the Listener event

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this); // This is the new line
        $this->saveDefaultConfig(); // Saves config.yml if not created.
        $this->reloadConfig(); // Fix bugs sometimes by getting configs values
    }
    public function onLoad(){
      $this->reloadConfig();
    }
    public function DeathMSG($msgderive, $entity, $name, $player){
      switch($msgderive){
        case "death.attack.generic":
          $genericdeath = str_replace("{name}", "$name", $this->getConfig()->get("generic"));
          $this->getServer()->broadcastMessage($genericdeath);
          break;
        case "death.attack.player":
          $playerdeat = str_replace("{name}", "$name", $this->getConfig()->get("player"));
          $playerdeath = str_replace("{killer}", $entity->getLastDamageCause()->getDamager()->getDisplayName(), $playerdeat);
          $playerdeath1 = str_replace("{weapon}", $entity->getLastDamageCause()->getDamager()->getInventory()->getItemInHand()->getName(), $playerdeath);
          $this->getServer()->broadcastMessage($playerdeath1);
          break;
        case "death.attack.mob":
          $mobdeat = str_replace("{name}", "$name", $this->getConfig()->get("mob"));
          $mobdeath = str_replace("{killer}", $entity->getLastDamageCause()->getDamager()->getName(), $mobdeat);
          $this->getServer()->broadcastMessage($mobdeath);
          break;
        case "death.attack.outOfWorld":
          $outOfWorld = str_replace("{name}", "$name", $this->getConfig()->get("outOfWorld"));
          $this->getServer()->broadcastMessage($outOfWorld);
          break;
        case "death.attack.inWall":
          $suffocation = str_replace("{name}", "$name", $this->getConfig()->get("suffocation"));
          $this->getServer()->broadcastMessage($suffocation);
          break;
        case "death.attack.onFire":
          $onFire = str_replace("{name}", "$name", $this->getConfig()->get("onFire"));
          $this->getServer()->broadcastMessage($onFire);
          break;
        case "death.attack.inFire":
          $inFire = str_replace("{name}", "$name", $this->getConfig()->get("inFire"));
          $this->getServer()->broadcastMessage($inFire);
          break;

        case "death.attack.drown":
          $drown = str_replace("{name}", "$name", $this->getConfig()->get("drown"));
          $this->getServer()->broadcastMessage($drown);
          break;
        case "death.attack.magic":
          $magic = str_replace("{name}", "$name", $this->getConfig()->get("magic"));
          $this->getServer()->broadcastMessage($magic);
          break;
        case "death.attack.cactus":
          $cactus = str_replace("{name}", "$name", $this->getConfig()->get("cactus"));
          $this->getServer()->broadcastMessage($cactus);
          break;
        case "death.fell.accident.generic":
          $highplace = str_replace("{name}", "$name", $this->getConfig()->get("highplace"));
          $this->getServer()->broadcastMessage($highplace);
          break;
        case "death.attack.arrow":
          $arro = str_replace("{name}", "$name", $this->getConfig()->get("player"));
          $arrow = str_replace("{killer}", $entity->getLastDamageCause()->getDamager()->getName(), $arro);
          $this->getServer()->broadcastMessage($arrow);
          break;
        case "death.attack.lava":
          $lava = str_replace("{name}", "$name", $this->getConfig()->get("lava"));
          $this->getServer()->broadcastMessage($lava);
          break;
        case "death.attack.explosion.player":
          $explosio = str_replace("{name}", "$name", $this->getConfig()->get("explosion"));
          $explosion = str_replace("{killer}", $entity->getLastDamageCause()->getDamager()->getName(), $explosio);
          $this->getServer()->broadcastMessage($explosion);
          break;

      }

    }
    public function onDeath(PlayerDeathEvent $event){
      $event->setDeathMessage(null);
      $player = $event->getPlayer();
      $name = $player->getName();
      $entity = $event->getEntity();
      $msgderive = $event->deriveMessage($entity->getDisplayName(), $entity->getLastDamageCause());

      //$this->getServer()->broadcastMessage("[§aDEBUG§r] $msgderive");
      //$this->getLogger()->info($msgderive);
      $this->DeathMSG($msgderive, $entity, $name, $player);

    }

    // #immediate-respawn: false # Enable/disables immediate respawn. When you die it bypasses the death screen.
    //
    // # true: Enables keepInventory when immediateRespawn is on.
    // # false: Disables keepInventory when ImmediateRespawn is enabled.
    // #keepInventory: false
    //
    // #TitleDied: "§l§cYOU DIED!" # Title of Death
    // #SubTitleDied: "§r§eTeleporting to spawn" # Sub Title of death

    // public function onDamage(EntityDamageEvent $event) {
    //   if($this->getConfig()->get("immediate-respawn") == true){
    //     $player = $event->getEntity();
    //
    //     if($event->getFinalDamage() >= $player->getHealth()) {
    //       if($player instanceof Human){
    //         $event->setCancelled();
    //
    //         $player->setHealth($player->getMaxHealth());
    //         $player->addTitle($this->getConfig()->get("TitleDied"), $this->getConfig()->get("SubTitleDied"), 1, 100, 50);
    //         $name = $player->getName();
    //         $entity = $event->getEntity();
    //         if($this->getConfig()->get("keepInventory") == false){
    //           $inventory = $player->getInventory();
    //           $pos = $player->getPosition();
    //           $level = $player->getLevel();
    //           $inventory->dropContents($level,$pos);
    //         }
    //         $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
    //         //$this->getServer()->broadcastMessage(PlayerDeathEvent::deriveMessage($player->getDisplayName(), $player->getLastDamageCause()));
    //         $this->DeathMSG(PlayerDeathEvent::deriveMessage($player->getDisplayName(), $player->getLastDamageCause()), $entity, $name, $player);
    //
    //       }
    //     }
    //   }
    //
    // }


}
