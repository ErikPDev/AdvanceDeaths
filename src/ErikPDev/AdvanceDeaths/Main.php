<?php

namespace ErikPDev\AdvanceDeaths;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\{Player, Server};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Level;
use pocketmine\level\particle\HeartParticle;

use ErikPDev\AdvanceDeaths\{DeathContainer,API};
use ErikPDev\AdvanceDeaths\utils\{
  DatabaseProvider,
  Update,
  configUpdater,
  configValidator,
  leaderboardData
};
use ErikPDev\AdvanceDeaths\Commands\advancedeaths;
use ErikPDev\AdvanceDeaths\effects\{
  Creeper,
  Lighting
};
use ErikPDev\AdvanceDeaths\Listeners\{
  ScoreHUDListener,
  instantRespawn,
  EconomySupport,
  killsLeaderboard,
  deathsLeaderboard,
  killstreakLeaderBoard
};
use ErikPDev\AdvanceDeaths\webhook\discord;
use pocketmine\command\RemoteConsoleCommandSender;
use raklib\utils\InternetAddress;
use ErikPDev\AdvanceDeaths\WS\WebSocket;

class Main extends PluginBase implements Listener {

  /** @var DeathContainer */
  private $DeathContainer;
  /** @var DatabaseProvider */
  private $database;
  /** @var Main */
  public static $instance;
  /** @var bool */
  public $isUpdated;
  private $scoreHud;
  private $advanceDeathsCommand;
  private $leaderboardData;
  private $killsLeaderboard;
  private $deathsLeaderboard;
  private $killstreakLeaderBoard;
  private $internetAddress;
  private $websocketServer;
  public function onEnable() {
      $this->getServer()->getPluginManager()->registerEvents($this,$this);
      $this->configLoad();

      $this->saveResource("sqlite.sql", true);
      $this->saveResource("mysql.sql", true);
      $this->database = new DatabaseProvider($this);
      $this->database->prepare();

      $this->DeathContainer = new DeathContainer($this, $this->database);
      $this->featuresLoad();

      $this->advanceDeathsCommand = new advancedeaths($this, $this->database);
      $this->isUpdated = true;
      Server::getInstance()->getAsyncPool()->submitTask(new Update("AdvanceDeaths", "3.0"));
      
      // Discord Webhook
      if($this->getConfig()->get("DiscordEnabled") == true){
        $this->discord = new discord($this->getConfig()->get("discordWebHook"), $this);
      }


      $this->leaderboardData = new leaderboardData($this->database);
      $this->getServer()->getPluginManager()->registerEvents($this->leaderboardData, $this);
      
      if($this->getConfig()->get("KillsFEnableFloatingText") == true){
        $this->killsLeaderboard = new killsLeaderboard($this);
        $this->getServer()->getPluginManager()->registerEvents($this->killsLeaderboard, $this);
      }

      if($this->getConfig()->get("DeathsFEnableFloatingText") == true){
        $this->deathsLeaderboard = new deathsLeaderboard($this);
        $this->getServer()->getPluginManager()->registerEvents($this->deathsLeaderboard, $this);
      }

      if($this->getConfig()->get("KillstreaksFEnableFloatingText") == true){
        $this->killstreakLeaderBoard = new killstreakLeaderBoard($this);
        $this->getServer()->getPluginManager()->registerEvents($this->killstreakLeaderBoard, $this);
      }
  }
  
  public static function getInstance(){
    return self::$instance;
  }
  
  public function onDisable() {
    if(isset($this->database)) $this->database->close();
    if(isset($this->killsLeaderboard)) $this->killsLeaderboard->disableLeaderboard();
    if(isset($this->deathsLeaderboard)) $this->deathsLeaderboard->disableLeaderboard();
    if(isset($this->killstreakLeaderBoard)) $this->killstreakLeaderBoard->disableLeaderboard();
    if($this->getConfig()->get("DiscordEnabled") == true){
      $this->discord->sendMessage("[AdvanceDeaths] Plugin is disabled.");
    }
  }
  
  public function onLoad(){
    $this->reloadConfig();
    self::$instance = $this;
  }
  
  private function configLoad(){
    $this->saveDefaultConfig();
    $this->reloadConfig();
    if ($this->getConfig()->get("config-verison") != 2.4){
      if($this->getConfig()->get("config-verison") >= 2){
        $this->getLogger()->critical("Your config.yml file for AdvanceDeaths is outdated. Updating to lastest configuration.");
        $configUpdater = new configUpdater($this, $this->getConfig());
        $configUpdater->update();
        $this->reloadConfig();
      }else{
        $this->getLogger()->critical("Your config.yml file for AdvanceDeaths is outdated. Please use the new config.yml. To get it, delete/rename the the old one.");
        $this->getServer()->getPluginManager()->disablePlugin($this);
        return;
      }
    }
    $configValidator = new configValidator($this, $this->getConfig());
    $configValidator->Check();


    
  }

  private function featuresLoad(){
    if($this->getServer()->getPluginManager()->getPlugin("ScoreHud") != null){
      $this->scoreHud = new ScoreHUDListener($this->database);
      $this->getServer()->getPluginManager()->registerEvents($this->scoreHud, $this);
      $this->getLogger()->debug("ScoreHud support is enabled.");
    }

    if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI") != null){
      $this->getServer()->getPluginManager()->registerEvents(new EconomySupport($this), $this);
      $this->getLogger()->debug("EconomyAPI support is enabled.");
    }

    if($this->getConfig()->get("instant-respawn") == true){
      $this->getServer()->getPluginManager()->registerEvents(new instantRespawn(), $this);
      $this->getLogger()->debug("InstantRespawn is enabled.");
    }
  }
  
  public function JoinEvent(PlayerJoinEvent $event) : void{
    if($event->getPlayer()->isOp() == true){
      if($this->isUpdated == false){
        $event->getPlayer()->sendMessage("§bAdvance§cDeaths §6>§r §ePlease update AdvanceDeaths to the lastest verison from poggit.pmmp.io.");
      }
    }
  }
    /**
     * @priority HIGHEST
     * @ignoreCancelled false
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event){
      $player = $event->getPlayer();
      if(!$player instanceof Player) return;
      $name = $player->getName();
      $entity = $event->getEntity();
      $msgderive = $event->deriveMessage($entity->getDisplayName(), $entity->getLastDamageCause());
      $event->setDeathMessage(""); // Using BroadcastMessage instead.
      $this->DeathContainer->Translate($msgderive, $entity);
      if($event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent){
        if($this->getConfig()->get("Heal-Killer") == true and $entity->getLastDamageCause()->getDamager() instanceof Player and $msgderive == "death.attack.player"){
          $entity->getLastDamageCause()->getDamager()->setHealth($player->getMaxHealth());
          $entity->getLastDamageCause()->getDamager()->setFood($player->getMaxFood());
          $entity->getLastDamageCause()->getDamager()->sendMessage("§bAdvance§cDeaths §6>§r ".$this->getConfig()->get("HealMessage"));
        }
      }
      if($player->getLastDamageCause() instanceof EntityDamageByEntityEvent){
        $damager = $player->getLastDamageCause()->getDamager();
        if(!$damager instanceof Player) return;
        $this->database->IncrecementKill($damager->getUniqueId()->toString(), $damager->getName());
        $this->database->IncrecementDeath($player->getUniqueId()->toString(), $player->getName());
        $this->database->IncrecementKillstreak($damager->getUniqueId()->toString(), $damager->getName());
        $this->database->EndKillstreak($player->getUniqueId()->toString(), $player->getName());
      }
      if(in_array($player->getLevel()->getFolderName(), $this->getConfig()->get("NotOnWorlds"))){return;}
      if(strtolower( $this->getConfig()->get("onDeathEffect") ) == "none"){return;}
      switch (strtolower($this->getConfig()->get("onDeathEffect"))) {
        case 'creeperparticle':
          $CreeperEffect = new Creeper($player);
          $CreeperEffect->run();
          break;
        case "lighting":
          $LightingEffect = new Lighting($player);
          $LightingEffect->run();
          break;
        default:
          $this->getLogger()->critical("Unsupported Effect type; Change this asap or the plugin will break!");
          $this->getServer()->getPluginManager()->disablePlugin($this);
          break;
      }

    }

    /**
     * @priority HIGHEST
     * @ignoreCancelled true
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event) {
      $player = $event->getEntity();
      $entity = $event->getEntity();
      if($player instanceof Player && $player->isCreative()) return;
        
      if($event->isCancelled()){return;}
      if ($player instanceof Player && $this->getConfig()->get("Hitted-Hearts") == true && $event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent){
        $xd = (float) 1;
        $yd = (float) 1;
        $zd = (float) 1;
        $level = $player->getLevel();
        $pos = $player->getPosition();

        $count = 1;
        $data = null;
        $particle = new HeartParticle($pos, 0);

        for($i = 0; $i < $count; ++$i){
          $particle->setComponents($pos->x, $pos->y+0.5, $pos->z);
          $level->addParticle($particle);
        }
      }
    
    }


    public function onCommand(\pocketmine\command\CommandSender $sender, \pocketmine\command\Command $command, string $label, array $args) : bool{
      switch (strtolower( $command->getName() )) {
        case 'advancedeaths':
          return $this->advanceDeathsCommand->onCommand($sender, $command, $label, $args);
          break;
        
        case 'ads':
          return $this->advanceDeathsCommand->onCommand($sender, $command, $label, $args);
          break;
        
        case 'rconadvancedeathsmanager':
          if(!$sender instanceof RemoteConsoleCommandSender){
            $sender->sendMessage("§bAdvance§cDeaths§r §6>§c This is a rcon command only!");
            return true;
          }
          switch ($args[0]) {
            case 'kills':
              $sender->sendMessage(base64_encode(leaderboardData::getKillsLeaderboard()));
              break;
            
            case 'deaths':
              $sender->sendMessage(base64_encode(leaderboardData::getDeathsLeaderboard()));
              break;
            
            case 'killstreaks':
              $sender->sendMessage(base64_encode(leaderboardData::getKillstreaksLeaderboard()));
              break;
            
            case 'reecon':
              $sender->sendMessage(base64_encode("AdvanceDeaths is installed properly. Have fun!"));
              break;
            default:
              $sender->sendMessage(base64_encode("Contact the developer or create a ticket at the discord group. Something went wrong."));
              break;
          }
          
          return false;
        
      }

      return true;
      
    }

}
