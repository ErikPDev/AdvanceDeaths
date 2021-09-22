-- #! sqlite
-- #{ advancedeaths
-- #    { init
CREATE TABLE IF NOT EXISTS "AdvanceDeaths" (
	"UUID"	TEXT UNIQUE,
	"PlayerName" TEXT DEFAULT "?",
	"Kills"	INTEGER DEFAULT 0,
	"Deaths"	INTEGER DEFAULT 0,
	PRIMARY KEY("UUID")
)
-- #    }
-- #    { addKill
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT OR REPLACE INTO "AdvanceDeaths" ("UUID", "PlayerName", "Kills", "Deaths") VALUES (:UUID, :PlayerName,(ifnull((select Kills from AdvanceDeaths where UUID = :UUID), 1) + 1), ifnull((select Deaths from AdvanceDeaths where UUID = :UUID), 0));
-- #    }
-- #    { addDeath
-- # 	  :UUID string
-- # 	  :PlayerName string
INSERT OR REPLACE INTO "AdvanceDeaths" ("UUID", "PlayerName", "Deaths", "Kills") VALUES (:UUID, :PlayerName, (ifnull((select Deaths from AdvanceDeaths where UUID = :UUID), 1) + 1), ifnull((select Kills from AdvanceDeaths where UUID = :UUID), 0));
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
SELECT "PlayerName", "Kills" FROM "AdvanceDeaths" ORDER BY "Kills" DESC LIMIT 1;
-- #    }
-- #	{ ScoreBoardTOP5
SELECT "PlayerName", "Kills" FROM "AdvanceDeaths" ORDER BY "Kills" DESC LIMIT 5;
-- #    }
-- #}