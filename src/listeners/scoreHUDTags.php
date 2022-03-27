<?php


namespace ErikPDev\AdvanceDeaths\listeners;

use ErikPDev\AdvanceDeaths\utils\database\databaseProvider;
use pocketmine\event\Listener;
use Ifera\ScoreHud\event\TagsResolveEvent;

class scoreHUDTags implements Listener {

	public function onTagResolve(TagsResolveEvent $event){
		$player = $event->getPlayer();
		$tag = $event->getTag();

		$playerName = $player->getName();
		$tagName = $tag->getName();

		if($tagName == "advancedeaths.myDeaths" || $tagName == "advancedeaths.myKills" || $tagName == "advancedeaths.topKiller" || $tagName == "advancedeaths.kdr"){
			$tag->setValue("Imp...");
		}

	}

}