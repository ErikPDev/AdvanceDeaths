<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;

class EconomySupport implements Listener{
    private $plugin;
    public function __construct($plugin){$this->plugin = $plugin;}

    private function ModifyMoney(Player $player, $PlayerMoney, $OptionA){
        if($OptionA == "lose"){
            EconomyAPI::getInstance()->reduceMoney($player, $PlayerMoney); 
        }
        if($OptionA == "gain"){
            EconomyAPI::getInstance()->addMoney($player, $PlayerMoney);
        }
    }
    public function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if (!$player instanceof Player) return;
        $DeathMoneyConfig = $this->plugin->getConfig()->get("DeathMoneyConfig");
        $KillMoneyConfig = $this->plugin->getConfig()->get("KillMoneyConfig");
        $PlayerMoney = EconomyAPI::getInstance()->myMoney($player);
        if($DeathMoneyConfig["isEnabled"] == true){
            if(in_array($player->getWorld()->getFolderName(), $KillMoneyConfig["disabledOnWorlds"])) return;
            if($DeathMoneyConfig["ValueType"] == "lose"){
                $OptionA = "lost";
            }
            if($DeathMoneyConfig["ValueType"] == "gain"){
                $OptionA = "gained";
            }
            switch (strtolower($DeathMoneyConfig["type"])) {
                case "all":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou died and $OptionA $" . $PlayerMoney);
                    $this->ModifyMoney($player, $PlayerMoney, $DeathMoneyConfig["ValueType"]);
                    break;
                
                case "half":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou died and $OptionA $" . $playerMoney / 2);
                    $this->ModifyMoney($player, $playerMoney / 2, $DeathMoneyConfig["ValueType"]);
                    break;

                case "percent":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou died and $OptionA $" . ((double)$DeathMoneyConfig["amount"] / 100) * $PlayerMoney);
                    $this->ModifyMoney($player, ((double)$DeathMoneyConfig["amount"] / 100) * $PlayerMoney, $DeathMoneyConfig["ValueType"]);
                    break;

                case "amount":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou died and $OptionA $" . (double)$DeathMoneyConfig["amount"]);
                    $this->ModifyMoney($player, (double)$DeathMoneyConfig["amount"], $DeathMoneyConfig["ValueType"]);
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        if($KillMoneyConfig["isEnabled"] == true){
            if(in_array($player->getWorld()->getFolderName(), $KillMoneyConfig["disabledOnWorlds"])) return;
            if(!$event->getEntity()->getLastDamageCause() instanceof EntityDamageByEntityEvent) return;
            if(!$player->getLastDamageCause()->getDamager() instanceof Player) return;
            
            if($KillMoneyConfig["ValueType"] == "lose"){
                $OptionA = "lost";
            }
            if($KillMoneyConfig["ValueType"] == "gain"){
                $OptionA = "gained";
            }
            switch (strtolower($KillMoneyConfig["type"])) {
                case "all":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . $PlayerMoney);
                    $this->ModifyMoney($player, $PlayerMoney, $KillMoneyConfig["ValueType"]);
                    break;
                
                case "half":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . $playerMoney / 2);
                    $this->ModifyMoney($player, $playerMoney / 2, $KillMoneyConfig["ValueType"]);
                    break;

                case "percent":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . ((double)$KillMoneyConfig["amount"] / 100) * $PlayerMoney);
                    $this->ModifyMoney($player, ((double)$KillMoneyConfig["amount"] / 100) * $PlayerMoney, $KillMoneyConfig["ValueType"]);
                    break;

                case "amount":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . (double)$KillMoneyConfig["amount"]);
                    $this->ModifyMoney($player, (double)$KillMoneyConfig["amount"], $KillMoneyConfig["ValueType"]);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    
    }
}