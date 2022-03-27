-- #! sqlite
-- #{ advancedeaths
-- #    { init
CREATE TABLE IF NOT EXISTS "AdvanceDeaths" (
    "UUID"	TEXT UNIQUE,
    "PlayerName" TEXT DEFAULT "?",
    "Kills"	INTEGER DEFAULT 0,
    "Deaths"	INTEGER DEFAULT 0,
    "Killstreak" INTEGER DEFAULT 0,
    PRIMARY KEY("UUID")
)
    -- #    }
-- #    { increaseKill
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT OR REPLACE INTO "AdvanceDeaths" ("UUID", "PlayerName", "Kills", "Deaths", "Killstreak") VALUES (:UUID, :PlayerName,(ifnull((select Kills from AdvanceDeaths where UUID = :UUID), 1) + 1), ifnull((select Deaths from AdvanceDeaths where UUID = :UUID), 0), ifnull((select Killstreak from AdvanceDeaths where UUID = :UUID), 0));
-- #    }
-- #    { increaseDeath
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT OR REPLACE INTO "AdvanceDeaths" ("UUID", "PlayerName", "Deaths", "Kills", "Killstreak") VALUES (:UUID, :PlayerName, (ifnull((select Deaths from AdvanceDeaths where UUID = :UUID), 1) + 1), ifnull((select Kills from AdvanceDeaths where UUID = :UUID), 0), ifnull((select Killstreak from AdvanceDeaths where UUID = :UUID), 0));
-- #    }
-- #    { increaseKillstreak
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT OR REPLACE INTO "AdvanceDeaths" ("UUID", "PlayerName", "Deaths", "Kills", "Killstreak") VALUES (:UUID, :PlayerName, ifnull((select Deaths from AdvanceDeaths where UUID = :UUID), 0), ifnull((select Kills from AdvanceDeaths where UUID = :UUID), 0), (ifnull((select Killstreak from AdvanceDeaths where UUID = :UUID), 0)+1));
-- #    }
-- #    { ResetKillstreak
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT OR REPLACE INTO "AdvanceDeaths" ("UUID", "PlayerName", "Deaths", "Kills", "Killstreak") VALUES (:UUID, :PlayerName, ifnull((select Deaths from AdvanceDeaths where UUID = :UUID), 0), ifnull((select Kills from AdvanceDeaths where UUID = :UUID), 0), 0);
-- #    }
-- #	{ getKills
-- # 	  :PlayerName string
SELECT Kills FROM AdvanceDeaths WHERE PlayerName = :PlayerName
-- #    }
-- #	{ getDeaths
-- # 	  :PlayerName string
SELECT Deaths FROM AdvanceDeaths WHERE PlayerName = :PlayerName
-- #    }
-- #	{ getKills&Deaths&Killstreak
-- # 	  :PlayerName string
SELECT Deaths, Kills, Killstreak,PlayerName FROM AdvanceDeaths WHERE lower(PlayerName) LIKE lower(:PlayerName)
-- #    }
-- #	{ getKillstreak
-- # 	  :PlayerName string
SELECT Killstreak FROM AdvanceDeaths WHERE PlayerName = :PlayerName
-- #    }
-- #	{ ScoreBoardTOP
SELECT "PlayerName", "Kills" FROM "AdvanceDeaths" ORDER BY "Kills" DESC LIMIT 1;
-- #    }
-- #	{ Top5Kills
SELECT "PlayerName", "Kills" FROM "AdvanceDeaths" ORDER BY "Kills" DESC LIMIT 5;
-- #    }
-- #	{ Top5Deaths
SELECT "PlayerName", "Deaths" FROM "AdvanceDeaths" ORDER BY "Deaths" DESC LIMIT 5;
-- #    }
-- #	{ Top5Killstreaks
SELECT "PlayerName", "Killstreak" FROM "AdvanceDeaths" ORDER BY "Killstreak" DESC LIMIT 5;
-- #    }
-- #}