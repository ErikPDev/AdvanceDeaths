<?php
namespace ErikPDev\AdvanceDeaths\utils;

class configUpdater{
    private $oldConfig,$plugin;
    public function __construct($plugin, $oldConfig){
        $this->plugin = $plugin;
        $this->oldConfig = $oldConfig;
    }

    public function update(){
        $newConfig = yaml_parse(stream_get_contents($this->plugin->getResource("config.yml")));
        $plainConfig = stream_get_contents($this->plugin->getResource("config.yml"));
        foreach ($this->oldConfig->getAll() as $key => $value) {
            if(is_array($value)) continue;
            if($key == "config-verison") continue;
            if(!array_key_exists($key, $newConfig)) continue;
            $plainConfig = str_replace($newConfig[$key], $value, $plainConfig);
        }
        $UpdateOldConfig = fopen($this->plugin->getDataFolder()."config.yml", "w");
        fwrite($UpdateOldConfig, $plainConfig);
        fclose($UpdateOldConfig);
    }
}