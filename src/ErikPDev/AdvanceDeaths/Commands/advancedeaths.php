<?php

namespace ErikPDev\AdvanceDeaths\Commands;

use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use jojoe77777\FormAPI\{CustomForm};
use ErikPDev\AdvanceDeaths\utils\DatabaseProvider;
class advancedeaths{
    
    private $plugin,$database;
    
    public function __construct($plugin, $database){$this->plugin = $plugin;$this->database = $database;}
    
    public function onCommand(CommandSender $player, Command $cmd, string $label, array $args) : bool{
        if(!strtolower( $cmd->getName() ) == "advancedeaths" && !strtolower($cmd->getName()) == "ads") return false;
        if(!$player instanceof Player){$player->sendMessage("§bAdvance§cDeaths §6>§c Please run the command from in-game.");return false;}
        $form = new CustomForm(function (\pocketmine\player\Player $player, $data) use (&$PlayerNames){
            if(!isset($data[2])){return $player->sendMessage("§bAdvance§cDeaths §6>§r No Player Name has been selected.");}
            if($data[2] > count($PlayerNames)){return $player->kick("             §b~ Advance§cDeaths ~\nKicked for attempted data manipulation.",false);}
            $PLYR = Server::getInstance()->getOfflinePlayer($PlayerNames[$data[2]]);
            $this->database->getDatabase()->executeSelect(DatabaseProvider::GETKILLS_AND_DEATHS, ["UUID" => $PLYR->getUniqueID()->toString()],
                function(array $rows) use (&$PLYR, &$player){
                    $deaths = $rows[0]["Deaths"] ?? 0;
                    $kills = $rows[0]["Kills"] ?? 0;
                    $playerName = $PLYR->getName();
                    $player->sendMessage("§bAdvance§cDeaths §rStats §6>\n"."§bPlayer Name: §r$playerName\n§cKills§r: ".(string)$kills."\n§6Deaths: §r".(string)$deaths."\n§aKDR:§r ".DatabaseProvider::getKillToDeathRatio($kills, $deaths));
                });
            
        });
            $form->setTitle("§bAdvance§cDeaths §rStats");
            
            foreach(Server::getInstance()->getOnlinePlayers() as $PLAr){
                $PlayerNames[] = $PLAr->getName();
            }
            $form->addLabel("Verison: \n    ".$this->plugin->getFullName());
            $form->addDropdown("Player name:", $PlayerNames);
            $player->sendForm($form);
            
            return true;
            
    }
}