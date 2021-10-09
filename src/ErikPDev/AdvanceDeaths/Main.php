<?php

namespace ErikPDev\AdvanceDeaths;

use pocketmine\plugin\PluginBase;
use pocketmine\{Player, Server};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Level;
use pocketmine\level\particle\HeartParticle;

use ErikPDev\AdvanceDeaths\{DeathContainer,API};
use ErikPDev\AdvanceDeaths\utils\{DatabaseProvider,Update,configUpdater};
use ErikPDev\AdvanceDeaths\effects\{
  Creeper,
  Lighting
};
use ErikPDev\AdvanceDeaths\Listeners\{
  ScoreHUDListener,
  instantRespawn
};

use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
class Main extends PluginBase implements Listener {

  /** @var DeathContainer */
  private $DeathContainer;
  /** @var DatabaseProvider */
  private $database;
  /** @var Main */
  public static $instance;
  /** @var bool */
  public $isUpdated;
  /** @var FloatingTextParticle */
  private $KillsLeaderBoard;
  /** @var bool */
  private $FloatingTxtSupported;
  /** @var string */
  private $world;
  private $scoreHud;
  public function onEnable() {
      $this->getServer()->getPluginManager()->registerEvents($this,$this);
      $this->saveDefaultConfig();
      $this->reloadConfig();
      if ($this->getConfig()->get("config-verison") != 2.1){
        if($this->getConfig()->get("config-verison") == 2){
          $this->getLogger()->critical("Your config.yml file for AdvanceDeaths is outdated. Updating to lastest configuration.");
          $configUpdater = new configUpdater($this, $this->getConfig());
          $configUpdater->update();
        }else{
          $this->getLogger()->critical("Your config.yml file for AdvanceDeaths is outdated. Please use the new config.yml. To get it, delete/rename the the old one.");
          $this->getServer()->getPluginManager()->disablePlugin($this);
          return;
        }
      }
      $this->saveResource("sqlite.sql");
      $this->saveResource("mysql.sql");
      $this->database = new DatabaseProvider($this);
      $this->database->prepare();
      $this->DeathContainer = new DeathContainer($this, $this->database);
      if($this->getServer()->getPluginManager()->getPlugin("ScoreHud") != null){
        $this->scoreHud = new ScoreHUDListener($this->database);
        $this->getServer()->getPluginManager()->registerEvents($this->scoreHud, $this);
        $this->getLogger()->debug("ScoreHud support is enabled.");
      }
      if($this->getConfig()->get("instant-respawn") == true){
        $this->getServer()->getPluginManager()->registerEvents(new instantRespawn(), $this);
        $this->getLogger()->debug("InstantRespawn is enabled.");
      }

      $this->isUpdated = true;
      Server::getInstance()->getAsyncPool()->submitTask(new Update("AdvanceDeaths", "2.5"));
      

      $this->FloatingTxtSupported = $this->getConfig()->get("FEnableFloatingText");
      if($this->FloatingTxtSupported !== true){return;}
      $pos = $this->getConfig()->get("FLeaderBoardCoordinates");
      $this->world = $this->getConfig()->get("FLeaderboardWorld");
      if(!$this->getServer()->isLevelLoaded($this->world)) {
        $this->getServer()->loadLevel($this->world);
      }
      if(!$this->getServer()->getLevelByName($this->world)->isChunkLoaded($pos["X"] >> 4, $pos["Z"] >> 4)) {
        $this->getServer()->getLevelByName($this->world)->loadChunk($pos["X"] >> 4, $pos["Z"] >> 4);
      }
      
      $this->KillsLeaderBoard = new FloatingTextParticle(new Vector3($pos["X"],$pos["Y"],$pos["Z"]), "Loading...", "§bAdvance§cDeaths§r");
      $this->KillsLeaderBoard->setInvisible(false);
      $this->updateLeaderboard();
  }
  
  public static function getInstance(){
    return self::$instance;
  }
  
  public function onDisable() {
    if(isset($this->database)) $this->database->close();
    if(isset($this->KillsLeaderBoard)) $this->KillsLeaderBoard->setInvisible(true);
  }
  
  public function onLoad(){
    $this->reloadConfig();
    self::$instance = $this;
  }
  
  public function updateLeaderboard(){
    $KillsLeaderBoard = $this->KillsLeaderBoard;
    $this->database->getDatabase()->executeSelect(DatabaseProvider::SCOREBOARD_TOP5,[], 
      function(array $rows) use ($KillsLeaderBoard){
        $LeaderBoardText = "";
        foreach ($rows as $X => $Element) {
          $LeaderBoardText .= strval($X+1).". ".$Element["PlayerName"]." - Kills: ".strval($Element["Kills"])."\n";
        }
        $KillsLeaderBoard->setText($LeaderBoardText);
        Server::getInstance()->getLevelByName($this->world)->addParticle($KillsLeaderBoard);
      });
    
  }
  
  public function JoinEvent(PlayerJoinEvent $event) : void{
    if($event->getPlayer()->isOp() == true){
      if($this->isUpdated == false){
        $event->getPlayer()->sendMessage("§bAdvance§cDeaths §6>§r §ePlease update AdvanceDeaths to the lastest verison from poggit.pmmp.io.");
      }
    }
    if($this->FloatingTxtSupported == true) $this->getServer()->getLevelByName("world")->addParticle($this->KillsLeaderBoard, [$event->getPlayer()]);
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
        if($this->FloatingTxtSupported == true) $this->updateLeaderboard();
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



}
