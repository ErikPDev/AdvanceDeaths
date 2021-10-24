# AdvanceDeaths
[![](https://poggit.pmmp.io/shield.state/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths) [![](https://poggit.pmmp.io/shield.dl.total/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths) [![](https://poggit.pmmp.io/shield.dl/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths) [![](https://poggit.pmmp.io/shield.api/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths)

Discord: https://discord.gg/96yKvdDxrR

A plugin to take advantage of the Minecraft Death to improve your server.
With this plugin you can customize death messages with advanced keywords.
Vanilia like Instant Respawn, KDR, Kill and Death stats, including a Leaderboard!

Read setup for more info!

# Important
When you are updating AdvanceDeaths to V3.0 make sure you delete the previous database.

# Setup
The setup is really easy, you'll just need to download the phar file at the plugins folder.
After that reboot/start your server up, all configurations are now editable at the config.yml located at plugin_data/AdvanceDeaths. 
If you own a sql server the set the parameters in the config.yml

# Features
Features:
* Death Messages
  * You can modify all the death messages with variables
* EconomyAPI Support
  * Gain/Lose money on both Death and Kill.
* Commands
  * Commands to see other player stats
* Configuration
  * All Configurations will be automatically updated from 2.0.0 to 2.5
  * Configuration Validator to prevent crashes caused by mis-configured config file.
* AI Mobs
  * Pure Entities is now supported.
* ScoreHud
  * You can display a player Kills, Deaths or KDR on a scoreHud
  * Multiworld is now supported
* Instant Respawn
  * Instant Respawn is now client sided unlike other plugins!
* Leaderboards
  * There are 3 leaderboards available that can be enabled on the config file. They include Kills, Killstreaks, and deaths leaderboards.
* Effects
  * There are currently two effects. `CreeperParticle` and `Lighting`.
* Databases
  * AdvanceDeaths supports SQLite and MySql Databases
  * SQLite is preferred over MySql.
  * Database queries are asynchronous
* Particles
  * You can enable hitted-hearts on config.yml to show hearts when a player is hit.
* Killer Effetcs
  * You can heal the killer after death.
* Discord
  * You can enable discord webhooks to display death messages in your discord server.
  * You can also have support for the Discord Bot to get the ingame leaderboards.

# Setting up the discord bot
Steps:

1] Enable RCON in your server from server.properties

2] Add the AdvanceDeaths plugin to your server. Version 3 or higher should be installed.

3] Add the bot to your server using this link. https://discord.com/api/oauth2/authorize?client_id=900282852577509436&permissions=537259089&scope=bot

4] Run >help to see the list of command or run >setup to set your server up.

If you ran into any errors, bugs, something isn't working or if you need help.

Join the discord server.

# Bug Report
Found a bug?
- Head to [Issues](https://github.com/ErikPDev/AdvanceDeaths/issues)
- Tap on [New Issue](https://github.com/ErikPDev/AdvanceDeaths/issues/new)
- Write information about the bug.
- Send Issue
- The end

# ScoreHUD
| Tag                     | Description             |
|-------------------------|-------------------------|
| advancedeaths.myDeaths  | Get's the player Deaths |
| advancedeaths.myKills   | Get's the player Kills  |
| advancedeaths.topKiller | Shows the Top Killer    |
| advancedeaths.kdr       | Shows the player KDR    |

# Config Variables
| Tag name              | Description                             | Death Types that can be used on.   |
|-----------------------|-----------------------------------------|------------------------------------|
| {name}                | The player that got killed.             | All Death Types                    |
| {killer}              | The player who killed {name}            | Player, Mob, Arrow, and explosion. |
| {killerCurrentHealth} | Shows the killer's health               | Player                             |
| {killerMaxHealth}     | Shows the killer's Max Health           | Player                             |
| {weapon}              | What's the weapon name the killer used. | Player                             |
| {killer_kills}        | Kills of the Killer                     | Player                             |
| {player_kills}        | Kills of the player                     | Player                             |
| {killer_deaths}       | Deaths of the Killer                    | Player                             |
| {player_deaths}       | Deaths of the player                    | Player                             |
| {killer_kdr}          | KDR of the killer                       | Player                             |
| {player_kdr}          | KDR of the player                       | Player                             |
| {killer_killstreak}   | Killstreak of the killer                | Player                             |

# Commands
| Command       | Description                 | Permission        | Default |
|---------------|-----------------------------|-------------------|---------|
| advancedeaths | See an online player stats. | advancedeaths.use | True    |
| ads           | See an online player stats. | advancedeaths.use | True    |

# Photos
<img src="https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/Kill.jpg">
<img src="https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/Deaths.jpg">
<img src="https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/Killstreak.png">
<img src="https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/ScoreBoard.png">
<img src="https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/Form.png">

# Effects
There are currently two effects supported.
These are `CreeperParticle`, and `Lighting`. 
Feel free to add more Types in Github.

# Credits
Special thank to minijaham for helping me out with this project, and Primus for introducing me to Psuedo.

# API
Usage example:
`$AdvanceDeathDatabase = new \ErikPDev\AdvanceDeaths\utils\DatabaseProvider($AdvanceDeathsMain);`
Check out the source code to see how to use it, or create an issue.

# Used by:
Create an issue or open an ticket on discord to add you server to this list.

# Minecraft color coding
Color coding the death messages is possible!
Here's a list of color codes.
| Color Name   | Code |
|--------------|------|
| Dark Red     | §4   |
| Red          | §c   |
| Gold         | §6   |
| Yellow       | §e   |
| Dark Yellow  | §g   |
| Dark Green   | §2   |
| Green        | §a   |
| Aqua         | §b   |
| Dark Aqua    | §3   |
| Dark Blue    | §1   |
| Blue         | §9   |
| Light Purple | §d   |
| Dark Purple  | §5   |
| White        | §f   |
| Gray         | §7   |
| Dark Gray    | §8   |
| Black        | §0   |
