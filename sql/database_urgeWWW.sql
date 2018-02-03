
DROP SCHEMA

IF EXISTS urgedb;
	CREATE SCHEMA urgedb COLLATE = utf8_general_ci;

USE urgedb;


CREATE TABLE `Users` (
  `userid` VARCHAR(32),
  `email` VARCHAR(64),
  `password` VARCHAR(64),
  `usertype` ENUM("admin","teacher", "student"),
  `wannabe` BOOLEAN,
  PRIMARY KEY (`userid`)
);

CREATE TABLE `Video` (
  `videoid` CHAR(32),
  `user` VARCHAR(64),
  `name` VARCHAR(64),
  `descr` VARCHAR(512),
  `mime` VARCHAR(32),
  `views` BIGINT,
  `time` TIMESTAMP,
  PRIMARY KEY (`videoid`),
  FOREIGN KEY (`user`) REFERENCES Users(userid)
);

CREATE TABLE `UserLike` (
  `userid` VARCHAR(32),
  `video` CHAR(32),
  `vote` BOOLEAN,
  FOREIGN KEY (`userid`) REFERENCES Users(userid),
  FOREIGN KEY (`video`) REFERENCES Video(videoid)
);

CREATE TABLE `Playlist` (
  `playlistid` BIGINT,
  `user` VARCHAR(64),
  `title` VARCHAR(64),
  `desc` VARCHAR(512),
  PRIMARY KEY (`playlistid`),
  FOREIGN KEY (`user`) REFERENCES Users(userid)
);

CREATE TABLE `UserSubscribe` (
  `userid` VARCHAR(64),
  `playlistid` BIGINT,
  FOREIGN KEY (`userid`) REFERENCES Users(userid),
  FOREIGN KEY (`playlistid`) REFERENCES Playlist(playlistid)
);

CREATE TABLE `Comments` (
  `commentid` BIGINT,
  `user` VARCHAR(64),
  `video` CHAR(32),
  `comment` VARCHAR(64),
  `time` TIMESTAMP,
  PRIMARY KEY (`commentid`),
  FOREIGN KEY (`user`) REFERENCES Users(userid),
  FOREIGN KEY (`video`) REFERENCES Video(videoid)
);

CREATE TABLE `VideoPlaylist` (
  `video` CHAR(32),
  `playlist` BIGINT,
  FOREIGN KEY (`video`) REFERENCES Video(videoid),
  FOREIGN KEY (`playlist`) REFERENCES Playlist(playlistid)
);
