<?php

namespace ErikPDev\AdvanceDeaths\Listeners;

use pocketmine\event\Listener;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

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
            if(in_array($player->getLevel()->getFolderName(), $KillMoneyConfig["disabledOnWorlds"])) return;
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
                    $this->ModifyMoney($player, $PlayerMoney / 2, $DeathMoneyConfig["ValueType"]);
                    break;

                case "percent":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou died and $OptionA $" . ((double)$DeathMoneyConfig["amount"] / 100) * $PlayerMoney);
                    $this->ModifyMoney($player, ((double)$DeathMoneyConfig["amount"] / 100) * $PlayerMoney, $DeathMoneyConfig["ValueType"]);
                    break;

                case "amount":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou died and $OptionA $" . (double)$DeathMoneyConfig["amount"]);
                    $this->ModifyMoney($player, (double)$DeathMoneyConfig["amount"], $DeathMoneyConfig["ValueType"]);
                    break;

                case "playermoney":
                    $player->sendMessage("§bAdvance§cDeaths §6>§r §aYou died and $OptionA $" . (double)$PlayerMoney);
                    $this->ModifyMoney($player, (double) $PlayerMoney, $DeathMoneyConfig["ValueType"]);
                    break;

                default:
                    # code...
                    break;
            }
        }

        if($KillMoneyConfig["isEnabled"] == true){
            if(in_array($player->getLevel()->getFolderName(), $KillMoneyConfig["disabledOnWorlds"])) return;
            if(!$player->getLastDamageCause() instanceof EntityDamageByEntityEvent) return;
            if(!$player->getLastDamageCause()->getDamager() instanceof Player) return;
            $Killer = $player->getLastDamageCause()->getDamager();
            $KillerMoney = EconomyAPI::getInstance()->myMoney($Killer);
            
            if($KillMoneyConfig["ValueType"] == "lose"){
                $OptionA = "lost";
            }
            if($KillMoneyConfig["ValueType"] == "gain"){
                $OptionA = "gained";
            }
            switch (strtolower($KillMoneyConfig["type"])) {
                case "all":
                    $Killer->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . $KillerMoney);
                    $this->ModifyMoney($Killer, $KillerMoney, $KillMoneyConfig["ValueType"]);
                    break;
                
                case "half":
                    $Killer->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . $KillerMoney / 2);
                    $this->ModifyMoney($Killer, $KillerMoney / 2, $KillMoneyConfig["ValueType"]);
                    break;

                case "percent":
                    $Killer->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . ((double)$KillMoneyConfig["amount"] / 100) * $KillerMoney);
                    $this->ModifyMoney($Killer, ((double)$KillMoneyConfig["amount"] / 100) * $KillerMoney, $KillMoneyConfig["ValueType"]);
                    break;

                case "amount":
                    $Killer->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . (double)$KillMoneyConfig["amount"]);
                    $this->ModifyMoney($Killer, (double)$KillMoneyConfig["amount"], $KillMoneyConfig["ValueType"]);
                    break;
                
                case "playermoney":
                    $Killer->sendMessage("§bAdvance§cDeaths §6>§r §aYou killed and $OptionA $" . (double)$PlayerMoney);
                    $this->ModifyMoney($Killer, $PlayerMoney, $KillMoneyConfig["ValueType"]);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    
    }
}