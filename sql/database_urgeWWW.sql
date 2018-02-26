
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
ALTER TABLE `User` ADD UNIQUE KEY `email_index` (`email`);


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
  `rank`          int(11)     NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`videoid`) REFERENCES Video(id),
  FOREIGN KEY (`playlistid`) REFERENCES Playlist(id)
);
ALTER TABLE `VideoPlaylist` ADD UNIQUE KEY `videoid_playlistid_rank_unique_index` (`videoid`,`playlistid`, `rank`);


/*================================INSERT ADMIN TO USER=====================================*/;
INSERT INTO `user` (`id`, `fullname`, `email`, `password`, `usertype`, `wannabe`)
VALUES ('1337ADMIN1337', 'admin', 'admin@metoobe.com', '$2y$10$9rq9PYGjeLLgMvLeUw5GUOaDRThu8W6mgjtuTdhR3dNupjTrCJEa2', 'admin', 0);
