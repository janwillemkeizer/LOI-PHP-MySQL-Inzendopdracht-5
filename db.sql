SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
CREATE DATABASE IF NOT EXISTS dbLOI DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE dbLOI;

CREATE TABLE IF NOT EXISTS Games (
  GameID int(11) NOT NULL AUTO_INCREMENT,
  Date date NOT NULL,
  Home_Team int(11) NOT NULL,
  Away_Team int(11) NOT NULL,
  Home_Team_Score int(11) DEFAULT NULL,
  Away_Team_Score int(11) DEFAULT NULL,
  PRIMARY KEY (GameID),
  KEY Away_Team (Away_Team),
  KEY Home_Team (Home_Team)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Teams (
  TeamID int(11) NOT NULL AUTO_INCREMENT,
  Name varchar(30) NOT NULL,
  City varchar(30) NOT NULL,
  PRIMARY KEY (TeamID)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO Teams (TeamID, `Name`, City) VALUES
(1, 'Ajax', 'Amsterdam'),
(2, 'Feyenoord', 'Rotterdam'),
(3, 'PSV', 'Eindhoven'),
(4, 'AZ', 'Alkmaar'),
(5, 'FC Utrecht', 'Utrecht'),
(6, 'FC Groningen', 'Groningen');

CREATE TABLE IF NOT EXISTS Users (
  UserID int(11) NOT NULL AUTO_INCREMENT,
  Username varchar(32) NOT NULL,
  Password varchar(32) NOT NULL,
  Email varchar(50) NOT NULL,
  PRIMARY KEY (UserID),
  UNIQUE KEY Username (Username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE Games
  ADD CONSTRAINT games_ibfk_1 FOREIGN KEY (Away_Team) REFERENCES Teams (TeamID),
  ADD CONSTRAINT games_ibfk_2 FOREIGN KEY (Home_Team) REFERENCES Teams (TeamID);
