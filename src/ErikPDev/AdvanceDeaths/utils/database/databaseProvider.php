<?php

namespace ErikPDev\AdvanceDeaths\utils\database;

use ErikPDev\AdvanceDeaths\ADMain;
use ErikPDev\AdvanceDeaths\leaderboards\events\leaderboardDataUpdate;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class databaseProvider implements Listener {

	/**
	 * @var DataConnector
	 */
	private static DataConnector $database;

	private static array $databaseConfiguration = array(
		"type" => "sqlite",
		"sqlite" => array(
			"file" => "data.sqlite"
		),
		"worker-limit" => 1
	);

	public static array $data;


	public function __construct() {

		self::$database = libasynql::create(ADMain::getInstance(), self::$databaseConfiguration, [
			"sqlite" => "sqlite.sql"
		]);
		self::$database->executeGeneric(databaseQueries::$prepareDatabase);

		$this->refreshData();

	}

	/**
	 * @internal This function should only be used by AdvanceDeaths.
	 */
	public static function close(): void {

		if (isset(self::$database)) self::$database->close();

	}

	/**
	 * Increase the kill count.
	 *
	 * Notice: You'll have to call the leaderboardDataUpdate event to update the leaderboard.
	 * @param string $UUID
	 * @param string $PlayerName
	 */
	public static function increaseKill(string $UUID, string $PlayerName): void {

		self::$database->executeInsert(databaseQueries::$increaseKill, ["UUID" => $UUID, "PlayerName" => $PlayerName]);

	}

	/**
	 * Increase the death count.
	 *
	 * Notice: You'll have to call the leaderboardDataUpdate event to update the leaderboard.
	 * @param string $UUID
	 * @param string $PlayerName
	 */
	public static function increaseDeath(string $UUID, string $PlayerName): void {

		self::$database->executeInsert(databaseQueries::$increaseDeath, ["UUID" => $UUID, "PlayerName" => $PlayerName]);

	}

	/**
	 * Increase the Killstreak count.
	 *
	 * Notice: You'll have to call the leaderboardDataUpdate event to update the leaderboard.
	 * @param string $UUID
	 * @param string $PlayerName
 	*/
	public static function increaseKillStreak(string $UUID, string $PlayerName): void {

		self::$database->executeInsert(databaseQueries::$increaseKillStreak, ["UUID" => $UUID, "PlayerName" => $PlayerName]);

	}

	/**
	 * Set the killstreak count to 0.
	 *
	 * Notice: You'll have to call the leaderboardDataUpdate event to update the leaderboard.
	 * @param string $UUID
	 * @param string $PlayerName
	 */
	public static function endKillStreak(string $UUID, string $PlayerName): void {

		self::$database->executeInsert(databaseQueries::$resetKillStreak, ["UUID" => $UUID, "PlayerName" => $PlayerName]);

	}

	/**
	 * Get a player's kill count.
	 * @deprecated Use databaseProvider->getAll() instead, this function will soon be removed.
	 * @param string $PlayerName
	 * @return Promise
	 */
	public static function getKills(string $PlayerName): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$getKills, ["PlayerName" => $PlayerName], function (array $data) use ($promise): void {

			if (count($data) == 0) {
				$promise->reject();
				return;
			}

			$promise->resolve($data[0]);

		});

		return $promise->getPromise();

	}

	/**
	 * Get a player's death count.
	 * @deprecated Use databaseProvider->getAll() instead, this function will soon be removed.
	 * @param string $PlayerName
	 * @return Promise
	 */
	public static function getDeaths(string $PlayerName): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$getDeaths, ["PlayerName" => $PlayerName], function (array $data) use ($promise): void {

			if (count($data) == 0) {
				$promise->reject();
				return;
			}

			$promise->resolve($data[0]);

		});

		return $promise->getPromise();

	}

	/**
	 * Get a player Killstreak
	 * @param string $PlayerName
	 * @return Promise
	 */
	public static function getKillstreaks(string $PlayerName): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$getKillStreak, ["PlayerName" => $PlayerName], function (array $data) use ($promise): void {

			if (count($data) == 0) {
				$promise->reject();
				return;
			}

			$promise->resolve($data[0]);

		});

		return $promise->getPromise();

	}

	/**
	 * Returns the Top Killer Username.
	 *
	 * Used in ScoreHud
	 * @return Promise
	 */
	public static function getTopKiller(): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$scoreboardTop, [], function (array $data) use ($promise): void {

			if (count($data) == 0) {
				$promise->reject();
				return;
			}

			$promise->resolve($data[0]);

		});

		return $promise->getPromise();

	}

	/**
	 * Returns all the data of a player.
	 * @param string $PlayerName
	 * @return Promise
	 */
	public static function getAll(string $PlayerName): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$getKillsDeathsKillstreak, ["PlayerName" => $PlayerName], function (array $data) use ($promise): void {

			if (count($data) == 0) {
				$promise->reject();
				return;
			}

			$promise->resolve($data[0]);

		});

		return $promise->getPromise();

	}

	/**
	 * Gets the Top 5 Killers.
	 * @return Promise
	 */
	public static function getTop5kills(): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$top5Kills, [], function (array $data) use ($promise): void {

			$promise->resolve($data);

		},
			function () use ($promise) {
				$promise->reject();
			});


		return $promise->getPromise();

	}

	/**
	 * Gets the Top 5 Players that died.
	 * @return Promise
	 */
	public static function getTop5deaths(): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$top5Deaths, [], function (array $data) use ($promise): void {

			$promise->resolve($data);

		},
			function () use ($promise) {
				$promise->reject();
			});


		return $promise->getPromise();

	}

	/**
	 * Gets the top 5 players with the highest KillStreak.
	 * @return Promise
	 */
	public static function getTop5killstreaks(): Promise {

		$promise = new PromiseResolver();

		self::$database->executeSelect(databaseQueries::$top5KillStreaks, [], function (array $data) use ($promise): void {

			$promise->resolve($data);

		},
			function () use ($promise) {
				$promise->reject();
			});


		return $promise->getPromise();

	}

	/**
	 * This function refreshes the leaderboard and DiscordBot data.
	 */
	public function refreshData(): void {

		self::getTop5kills()->onCompletion(function ($data) {
			self::$data["Kills"] = $data;
		}, function () {
		});

		self::getTop5deaths()->onCompletion(function ($data) {
			self::$data["Deaths"] = $data;
		}, function () {
		});

		self::getTop5killstreaks()->onCompletion(function ($data) {
			self::$data["Killstreak"] = $data;

			$ev = new leaderboardDataUpdate(self::$data);
			$ev->call();

		}, function () {
		});

	}

	/**
	 * Calculates the Kills to Death Ratio using the supplied arguments.
	 * @param int $kills
	 * @param int $deaths
	 * @return string
	 */
	public static function getKillToDeathRatio(int $kills, int $deaths): string {
		if ($deaths !== 0) {
			$ratio = $kills / $deaths;
			if ($ratio !== 0) {
				return number_format($ratio, 1);
			}
		}
		return "0.0";
	}

	public function deathEvent(PlayerDeathEvent $event) {

		$player = $event->getPlayer();

		self::endKillStreak($player->getUniqueId(), $player->getName());
		self::increaseDeath($player->getUniqueId(), $player->getName());

		$this->refreshData();

		/** @var EntityDamageByEntityEvent|EntityDamageEvent $damageCause */
		$damageCause = $event->getEntity()->getLastDamageCause();

		if (!$damageCause instanceof EntityDamageByEntityEvent) return;
		$murderer = $damageCause->getDamager();

		if (!$murderer instanceof Player) return;
		/** @var Player $murderer */

		self::increaseKillStreak($murderer->getUniqueId(), $murderer->getName());
		self::increaseKill($murderer->getUniqueId(), $murderer->getName());

	}

}