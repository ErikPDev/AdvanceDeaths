# Important
This plugin is no longer being maintained. If you're willing to maintain it, fork this repository.
Goodbye Pocketmine!

<h1 align="center"> AdvanceDeaths </h1>

<div align="center">
    <a href="https://poggit.pmmp.io/p/AdvanceDeaths">
        <img alt="Poggit Status" src="https://poggit.pmmp.io/shield.state/AdvanceDeaths">
        <img alt="PMMP Version" src="https://poggit.pmmp.io/shield.api/AdvanceDeaths">
        <img alt="Total Downloads" src="https://poggit.pmmp.io/shield.dl.total/AdvanceDeaths">
        <img alt="Downloads" src="https://poggit.pmmp.io/shield.dl/AdvanceDeaths">
    </a>
</div>

AdvanceDeaths is created to take advantage of the Death Feature in Minecraft: Bedrock Edition.

With AdvanceDeaths, you can customize the death experience of your players and more to come.


# Notice:
This version of AdvanceDeaths is different from the previous ones.

You will need to re-modify all the configuration files.

If you're using BedrockEconomy, make sure you're using the latest version of it.

# Setup
Setting up AdvanceDeaths is made easy.

## Modifying Death Messages
You can modify Death Messages by changing the `deathMessages.yml` located in `plugin_data\AdvanceDeaths`.

## Creating Particles
### Step 1:
- Download the `particleCreator.html` from the <a href="https://github.com/ErikPDev/AdvanceDeaths/tree/master/tools">tools folder</a>.
- Double tap the html file or open it in a web browser. Javascript is required.

### Step 2:
- After that, you can click the black color box to change it's color. You can also select Fill color to change it's background color.
- If the color of the box is white or rgb(255,255,255), then it will be rendered as invisible.

### Step 3:
- Once you've made your masterpiece, click on the `Generate` button. It will be downloaded.
- Save the JSON file to `plugin_data\AdvanceDeaths\scripts\particles`. Make sure to rename it to use it on `onDeathScript.yml`.

# Features
- onDeath Features
    - onDeathScript to modify it, located in `plugin_data\AdvanceDeaths\scripts`. 
        - Possible script commands are:
            - `particle(ParticleName, Player/PlayerKiller)`
            - `playsound(soundName, Player/PlayerKiller)`
            - `message(Message, Player/PlayerKiller)`
            - `heal(PlayerKiller)`
            - More will be added in the near future.
    - Modifiable Death Message.
    - Economies Support
        - Gain / Lose on Kill / Death
        - BedrockEconomy is currently supported. Capital support is coming soon.
    - Instant Respawn
    - Killstreaks Announcement.
- Player/Entity Damage
  - Blood FX - Configurable in the `config.yml`
- Leaderboards
    - Kills
    - Deaths
    - Killstreaks
    - All leaderboards are customizable from `leaderboards.yml` located at `plugin_data\AdvanceDeaths`.
- Commands
  - ScoreHUD
      - Shows the player's kills, deaths, and killstreaks.
      - Customizable in the `lang.yml` located at `plugin_data\AdvanceDeaths`.
  
# Bug Report
Found a bug? 
- Head to <a href="https://github.com/ErikPDev/AdvanceDeaths/issues">issues</a>.
- Tap on <a href="https://github.com/ErikPDev/AdvanceDeaths/issues/new?assignees=ErikPDev&labels=bug&template=bug_report.md&title=%5BBUG%5D">New Issue</a>
- Write information about the bug, then click on `Submit new issue`.
- After that, I'll look into it.

# Contribution
All contributions are welcomed, just make sure you follow the <a href="https://github.com/ErikPDev/AdvanceDeaths/blob/master/Formatting.md">formatting</a>.

# Variables

You can use variables to customize text to your own needs.

## deathMessages.yml
Here are the variables used to modify the death messages.

| Variable         | Description                        | Example output: |
|------------------|------------------------------------|-----------------|
| {victim}         | The player that died               | Steve           |
| {murderer}       | The player that killed the victim. | Alex            |
| {murdererHealth} | The value of the murderer health   | 5/20            |
| {itemUsed}       | The item used to murder the victim | Stick           |

# Commands
| Command       | Description                 | Permission        | Default |
|---------------|-----------------------------|-------------------|---------|
| advancedeaths | See an online player stats. | advancedeaths.use | True    |
| ads           | See an online player stats. | advancedeaths.use | True    |

