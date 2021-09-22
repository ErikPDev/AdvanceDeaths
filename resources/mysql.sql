-- #! mysql
-- #{ advancedeaths
-- #    { init
CREATE TABLE IF NOT EXISTS `AdvanceDeaths` (
     `UUID` VARCHAR(36) NOT NULL , 
     `PlayerName` VARCHAR(40) DEFAULT "?",
     `Kills` INT NOT NULL DEFAULT '0' , 
     `Deaths` INT NOT NULL DEFAULT '0' , 
     PRIMARY KEY (`UUID`)
     );
-- #    }
-- #    { addKill
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT INTO `AdvanceDeaths`(`UUID`, `PlayerName`, `Kills`, `Deaths`) VALUES (:UUID, :PlayerName, 0, 0) ON DUPLICATE KEY UPDATE `Kills` = `Kills`+1; 
-- #    }
-- #    { addDeath
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT INTO `AdvanceDeaths`(`UUID`, `PlayerName`, `Kills`, `Deaths`) VALUES (:UUID, :PlayerName, 0, 0) ON DUPLICATE KEY UPDATE `Deaths` = `Deaths`+1; 
-- #    }
-- #	{ getKills
-- # 	  :UUID string
SELECT Kills FROM AdvanceDeaths WHERE UUID = :UUID
-- #    }
-- #	{ getDeaths
-- # 	  :UUID string
SELECT Deaths FROM AdvanceDeaths WHERE UUID = :UUID
-- #    }
-- #	{ getKills&Deaths
-- # 	  :UUID string
SELECT Deaths, Kills FROM AdvanceDeaths WHERE UUID = :UUID
-- #    }
-- #	{ ScoreBoardTOP
SELECT `PlayerName`, `Kills` FROM `AdvanceDeaths` ORDER BY `Kills` DESC LIMIT 1;
-- #    }
-- #	{ ScoreBoardTOP5
SELECT `PlayerName`, `Kills` FROM `AdvanceDeaths` ORDER BY `Kills` DESC LIMIT 5;
-- #    }
-- #}