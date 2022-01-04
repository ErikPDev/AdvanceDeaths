<?php
namespace ErikPDev\AdvanceDeaths\webhook;
use pocketmine\Server;
use ErikPDev\AdvanceDeaths\webhook\sendMessageTask;
class discord{
    /** @var string */
    private $webhookURL;
    private $plugin;
    public function __construct(string $webHookURL,\ErikPDev\AdvanceDeaths\Main $plugin){
        if(!$this->isValid($webHookURL)) {$plugin->getLogger()->critical("Discord Webhook link is invalid."); return Server::getInstance()->getPluginManager()->disablePlugin($plugin);}
        $this->webhookURL = $webHookURL;
        $this->plugin = $plugin;
        $this->sendMessage("[AdvanceDeaths] Plugin has loaded.");
    }

    public function execute(){}

    private function isValid(string $webHookURL): bool{
		return str_starts_with(strtolower($webHookURL),"https://discord.com/api/webhooks/");
	}

    public function sendMessage(string $message){
        $message = array(
            "content" => $message,
            "embeds" => null,
            "username" => "AdvanceDeaths",
            "avatar_url" => "https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/icon.png"
        );
        Server::getInstance()->getAsyncPool()->submitTask(new sendMessageTask($message, $this->webhookURL, $this->plugin));
    }
}