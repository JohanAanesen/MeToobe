
DROP SCHEMA

IF EXISTS urgedb;
	CREATE SCHEMA urgedb COLLATE = utf8_general_ci;

USE urgedb;


CREATE TABLE `User` (
  `id` 				VARCHAR(64) NOT NULL,
  `email` 		VARCHAR(64) NOT NULL,
  `password` 	VARCHAR(64) NOT NULL,
  `usertype` 	ENUM("admin","teacher", "student") NOT NULL,
  `wannabe` 	BOOLEAN,
  PRIMARY KEY (`id`)
);

CREATE TABLE `Video` (
  `id` 					VARCHAR(64) NOT NULL,
  `userid` 			VARCHAR(64) NOT NULL,
  `name` 				VARCHAR(64) NOT NULL,
	`course`			VARCHAR(64),
	`topic`				VARCHAR(64),
  `description` VARCHAR(512),
  `mime` 				VARCHAR(64),
  `views` 			BIGINT NOT NULL,
  `time` 				TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userid`) REFERENCES User(id)
);

CREATE TABLE `UserLike` (
  `userid` 		VARCHAR(64) NOT NULL,
  `videoid` 	VARCHAR(64) NOT NULL,
  `vote` 			BOOLEAN NOT NULL,
  FOREIGN KEY (`userid`) REFERENCES User(id),
  FOREIGN KEY (`videoid`) REFERENCES Video(id)
);

CREATE TABLE `Playlist` (
  `id` 					VARCHAR(64) NOT NULL,
  `userid` 			VARCHAR(64) NOT NULL,
  `title` 			VARCHAR(64) NOT NULL,
  `description` VARCHAR(512),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userid`) REFERENCES User(id)
);

CREATE TABLE `UserSubscribe` (
  `userid` 			VARCHAR(64) NOT NULL,
  `playlistid` 	VARCHAR(64) NOT NULL,
  FOREIGN KEY (`userid`) REFERENCES User(id),
  FOREIGN KEY (`playlistid`) REFERENCES Playlist(id)
);

CREATE TABLE `Comment` (
  `id` 					VARCHAR(64) NOT NULL,
  `userid` 			VARCHAR(64) NOT NULL,
  `videoid` 		VARCHAR(64) NOT NULL,
  `comment` 		VARCHAR(512) NOT NULL,
  `time` 				TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userid`) REFERENCES User(id),
  FOREIGN KEY (`videoid`) REFERENCES Video(id)
);

CREATE TABLE `VideoPlaylist` (
  `videoid` 			VARCHAR(64) NOT NULL,
  `playlistid` 		VARCHAR(64) NOT NULL,
  FOREIGN KEY (`videoid`) REFERENCES Video(id),
  FOREIGN KEY (`playlistid`) REFERENCES Playlist(id)
);

/*=====================================================================INSERT ADMIN TO USER======================================================================*/;
INSERT INTO `user` (`id`, `email`, `password`, `usertype`, `wannabe`) VALUES ('1337ADMIN1337', 'admin@metoobe.com', '21232f297a57a5a743894a0e4a801fc3', 'admin', 0);
