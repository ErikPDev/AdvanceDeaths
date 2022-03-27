<?php

namespace ErikPDev\AdvanceDeaths\discord;

use ErikPDev\AdvanceDeaths\ADMain;
use ErikPDev\AdvanceDeaths\discord\commands\help;
use ErikPDev\AdvanceDeaths\discord\commands\playerInfo;
use ErikPDev\AdvanceDeaths\discord\commands\players;
use ErikPDev\AdvanceDeaths\discord\commands\topdeaths;
use ErikPDev\AdvanceDeaths\discord\commands\topkills;
use ErikPDev\AdvanceDeaths\discord\commands\topkillstreaks;
use JaxkDev\DiscordBot\Models\Activity;
use JaxkDev\DiscordBot\Models\Member;
use JaxkDev\DiscordBot\Models\Messages\Embed\Embed;
use JaxkDev\DiscordBot\Models\Messages\Embed\Field;
use JaxkDev\DiscordBot\Models\Messages\Embed\Footer;
use JaxkDev\DiscordBot\Models\Messages\Message;
use JaxkDev\DiscordBot\Plugin\Api;
use JaxkDev\DiscordBot\Plugin\Events\{DiscordClosed, DiscordReady, MessageSent};
use JaxkDev\DiscordBot\Plugin\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;


class discordListener implements Listener {

	/**
	 * @var Api
	 */
	private static Api $api;
	private static Config $discordBotConfig;
	public static string $prefix;

	private static array $commands;

	public function __construct() {

		self::$discordBotConfig = new Config(ADMain::getInstance()->getDataFolder() . "discordBot.yml");
		self::$prefix = self::$discordBotConfig->get("prefix", ">");

		/** @var Main $DiscordBot */
		$DiscordBot = ADMain::getInstance()->getServer()->getPluginManager()->getPlugin("DiscordBot");

		self::$api = $DiscordBot->getApi();

		self::$commands = array(
			"info" => new playerInfo("info", "Get a player information", self::$api, self::getTemplate("info")),
			"players" => new players("players", "All online players", self::$api, self::getTemplate("players")),
			"topkills" => new topkills("topkills", "Get top kills", self::$api, self::getTemplate("topkills")),
			"topdeaths" => new topdeaths("topdeaths", "Get top deaths", self::$api, self::getTemplate("topdeaths")),
			"topkillstreaks" => new topkillstreaks("topkillstreaks", "Get top killstreaks", self::$api, self::getTemplate("topkillstreaks")),
			"help" => new help("help", "All commands", self::$api, null)
		);

	}

	public static function getCommands(): array {
		return self::$commands;
	}

	public static function getTemplate(string $templateName): array|\ErrorException {

		$templates = self::$discordBotConfig->getNested("templates");

		if (!array_key_exists($templateName, $templates)) return throw new \ErrorException("Template named $templateName not found.");

		return $templates[$templateName];

	}

	public function onReady(DiscordReady $event) {

		$ac = new Activity("0 players & `".self::$prefix."`", Activity::TYPE_WATCHING);
		self::$api->updateBotPresence($ac, Member::STATUS_OFFLINE);

	}

	public function onClose(DiscordClosed $event) {

		ADMain::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
			function () use ($event) {

				if (!$event->getPlugin()->isEnabled()) {

					Server::getInstance()->getPluginManager()->enablePlugin($event->getPlugin());
					ADMain::getInstance()->getLogger()->info("Enabled DiscordBot");

				} else {

					ADMain::getInstance()->getLogger()->warning("Discord has closed but DiscordBot is still enabled.");

				}

			}
		), 20 * 20);

	}

	public function playerJoin(PlayerJoinEvent $event) {

		$ac = new Activity(count(Server::getInstance()->getOnlinePlayers()) . " players & `".self::$prefix."`", Activity::TYPE_WATCHING);
		self::$api->updateBotPresence($ac);

	}

	public function playerQuit(PlayerQuitEvent $event) {

		$ac = new Activity(count(Server::getInstance()->getOnlinePlayers()) - 1 . " players & `".self::$prefix."`", Activity::TYPE_WATCHING);
		self::$api->updateBotPresence($ac);

	}

	/**
	 * @priority MONITOR
	 */
	public function deathEvent(PlayerDeathEvent $event) {

		if (self::$discordBotConfig->get("deathsChannelID", 0) == 0) return;

		$this->sendMessage(self::$discordBotConfig->get("deathsChannelID"), preg_replace("/§./", "", $event->getDeathMessage()));

	}

	public function messageSent(MessageSent $event) {

		$content = $event->getMessage()->getContent();
		if (!str_starts_with($content, self::$prefix)) return;
		$args = explode(" ", substr(trim(strtolower($content)), 1));

		foreach (self::$commands as $command) {

			if ($command->getCommandName() !== $args[0]) continue;
			$command = self::$commands[$args[0]];
			array_shift($args);
			$command->run($event->getMessage(), $args);
			return;

		}

	}

	public function sendMessage(int $channelID, string $content) {

		try {
			self::$api->sendMessage(
				new Message(
					$channelID,
					null,
					$content
				)
			);
		} catch (\Throwable $exception) {
			ADMain::getInstance()->getLogger()->critical($exception->getMessage());
		}

	}


	public static function sendEmbeddedMessage(int $channelID, string $embedTitle, string $description, array $fields, int $color): void {

		try {
			$embed = new Embed(
				$embedTitle,
				null,
				$description,
				null,
				null,
				$color,
				new Footer("Powered by AdvanceDeaths")
			);

			$embedFields = [];
			foreach ($fields as $fieldName => $fieldValue) {
				$embedFields[] = new Field($fieldName, $fieldValue, true);
			}
			$embed->setFields($embedFields);

			$messageResponse = new Message(
				$channelID,
				null,
				"",
				$embed
			);

			self::$api->sendMessage($messageResponse);
		} catch (\Throwable $exception) {
			ADMain::getInstance()->getLogger()->critical($exception->getMessage());
		}

	}

}