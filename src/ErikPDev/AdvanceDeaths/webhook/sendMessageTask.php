<?php
namespace ErikPDev\AdvanceDeaths\webhook;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use ErikPDev\AdvanceDeaths\Main;

class sendMessageTask extends AsyncTask {

	/** @var string */
	protected $message;

    /** @var string */
	protected $webhook;

    /** @var Main */
	protected $plugin;
    
	public function __construct($message, $webhook){
		$this->message = $message;
        $this->webhook = $webhook;
	}

	public function onRun(){
		$ch = curl_init($this->webhook);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->message));
		curl_setopt($ch, CURLOPT_POST,true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
		$this->setResult([curl_exec($ch), curl_getinfo($ch, CURLINFO_RESPONSE_CODE)]);
		curl_close($ch);
	}

	public function onCompletion(Server $server){
		$response = $this->getResult();
		if(!in_array($response[1], [200, 204])){
			$server->getLogger()->error("[AdvanceDeaths] Got error ({$response[1]}): " . $response[0]);
		}
	}
}