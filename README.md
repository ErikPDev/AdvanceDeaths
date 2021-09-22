# AdvanceDeaths
[![](https://poggit.pmmp.io/shield.state/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths) [![](https://poggit.pmmp.io/shield.dl.total/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths) [![](https://poggit.pmmp.io/shield.dl/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths) [![](https://poggit.pmmp.io/shield.api/AdvanceDeaths)](https://poggit.pmmp.io/p/AdvanceDeaths)

A plugin to take advantage of the Minecraft Death to improve your server.
With this plugin you can customize death messages with advanced keywords.
Vanilia like Instant Respawn, KDR, Kill and Death stats, including a Leaderboard!

Read setup for more info!

# Setup
The setup is really easy, you'll just need to download the phar file at the plugins folder.
After that reboot/start your server up, all configurations are now editable at the config.yml located at plugin_data/AdvanceDeaths. 
If you own a sql server the set the parameters in the config.yml

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
I will add KDR on the next update.
# Photos
<img src="https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/FloatingText.png">
<img src="https://github.com/ErikPDev/AdvanceDeaths/raw/master/assets/ScoreBoard.png">

# Config file
If you are updating AdvanceDeaths, Make sure you rename the old file to something else other than `config.yml`

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
