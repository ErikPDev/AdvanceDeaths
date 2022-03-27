<?php

namespace ErikPDev\AdvanceDeaths\utils\database;

class databaseQueries {

	public static string $prepareDatabase = "advancedeaths.init";

	public static string $increaseKill = "advancedeaths.increaseKill";
	public static string $increaseDeath = "advancedeaths.increaseDeath";
	public static string $increaseKillStreak = "advancedeaths.increaseKillstreak";

	public static string $getKills = "advancedeaths.getKills";
	public static string $getDeaths = "advancedeaths.getDeaths";
	public static string $getKillsDeathsKillstreak = "advancedeaths.getKills&Deaths&Killstreak";
	public static string $getKillStreak = "advancedeaths.getKillstreak";

	public static string $scoreboardTop = "advancedeaths.ScoreBoardTOP";
	public static string $resetKillStreak = "advancedeaths.ResetKillstreak";

	public static string $top5KillStreaks = "advancedeaths.Top5Killstreaks";
	public static string $top5Kills = "advancedeaths.Top5Kills";
	public static string $top5Deaths = "advancedeaths.Top5Deaths";

}