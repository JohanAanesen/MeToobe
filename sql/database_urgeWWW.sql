
DROP SCHEMA

IF EXISTS urgedb;
	CREATE SCHEMA urgedb COLLATE = utf8_general_ci;

USE urgedb;

/* BASE TABLES
 *  - User
 *  - Video
 *  - Playlist
 *  - Comment
 */
CREATE TABLE `User` (
  `id` 				VARCHAR(64) NOT NULL,
	`fullname`	VARCHAR(64) NOT NULL,
  `email` 		VARCHAR(64) NOT NULL,
  `password` 	VARCHAR(64) NOT NULL,
  `usertype` 	ENUM("admin","teacher", "student") NOT NULL DEFAULT 'student',
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
  `views` 			BIGINT NOT NULL DEFAULT '0',
  `time` 				TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userid`) REFERENCES User(id)
);

CREATE TABLE `Playlist` (
  `id` 					VARCHAR(64) NOT NULL,
  `userid` 			VARCHAR(64) NOT NULL,
  `title` 			VARCHAR(64) NOT NULL,
  `description` VARCHAR(512),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`userid`) REFERENCES User(id)
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


/* LINKING TABLES (meta tables, foreign key tables)
 * - UserSubscribe
 * - UserLike
 * - VideoPlaylist
 */
CREATE TABLE `UserSubscribe` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `userid`     varchar(64) NOT NULL,
  `playlistid` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),  
  FOREIGN KEY (`userid`) REFERENCES User(id),
  FOREIGN KEY (`playlistid`) REFERENCES Playlist(id)
);
ALTER TABLE `UserSubscribe` ADD UNIQUE KEY `userid_playlistid_unique_index` (`userid`,`playlistid`);

CREATE TABLE `UserLike` (
  `id`        int(11)     NOT NULL AUTO_INCREMENT,
  `userid` 		VARCHAR(64) NOT NULL,
  `videoid` 	VARCHAR(64) NOT NULL,
  `vote` 			BOOLEAN NOT NULL,
  PRIMARY KEY (`id`),  
  FOREIGN KEY (`userid`) REFERENCES User(id),
  FOREIGN KEY (`videoid`) REFERENCES Video(id)
);
ALTER TABLE `UserLike`      ADD UNIQUE KEY `user_like_unique_index` (`userid`,`videoid`);

CREATE TABLE `VideoPlaylist` (
  `id`            int(11)     NOT NULL AUTO_INCREMENT,
  `videoid` 			VARCHAR(64) NOT NULL,
  `playlistid` 		VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`videoid`) REFERENCES Video(id),
  FOREIGN KEY (`playlistid`) REFERENCES Playlist(id)
);
ALTER TABLE `VideoPlaylist` ADD UNIQUE KEY `videoid_playlistid_unique_index` (`videoid`,`playlistid`);


/*================================INSERT ADMIN TO USER=====================================*/;
INSERT INTO `user` (`id`, `fullname`, `email`, `password`, `usertype`, `wannabe`) 
VALUES ('1337ADMIN1337', 'admin', 'admin@metoobe.com', '21232f297a57a5a743894a0e4a801fc3', 'admin', 0);

/*=================================== TEST DATA =================================*/

/* class PlaylistTest */
INSERT INTO `user` (`id`, `fullname`, `email`, `password`, `usertype`, `wannabe`) VALUES ('1337TEST1337', 'testuser', 'test@metoobe.com', '098f6bcd4621d373cade4e832627b4f6', 'student', 0);
INSERT INTO `playlist` (`id`, `userid`,`title`) VALUES ('testplaylist1', '1337TEST1337', 'Test Playlist');  
INSERT INTO `video` (`id`, `userid`, `name`) VALUES ('testvideo1', '1337TEST1337', 'Test Video 1');  
INSERT INTO `video` (`id`, `userid`, `name`) VALUES ('testvideo2', '1337TEST1337', 'TEst Video 2');  
INSERT INTO `video` (`id`, `userid`, `name`) VALUES ('testvideo3', '1337TEST1337', 'Test video 3');

INSERT INTO `VideoPlaylist`(`videoid`, `playlistid`)VALUES('testvideo1', 'testplaylist1');
INSERT INTO `VideoPlaylist`(`videoid`, `playlistid`)VALUES('testvideo2', 'testplaylist1');
INSERT INTO `VideoPlaylist`(`videoid`, `playlistid`)VALUES('testvideo3', 'testplaylist1');