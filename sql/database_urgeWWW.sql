CREATE TABLE `Users` (
  `username` VARCHAR(64),
  `password` VARCHAR(64),
  `brukertype` ENUM("admin","student", "teacher"),
  `wannabe` BOOLEAN,
  PRIMARY KEY (`username`)
);

CREATE TABLE `Video` (
  `videoid` BIGINT,
  `user` VARCHAR(64),
  `name` VARCHAR(64),
  `desc` VARCHAR(512),
  `views` BIGINT,
  `time` TIMESTAMP,
  PRIMARY KEY (`videoid`),
  FOREIGN KEY (`user`) REFERENCES Users(username)
);

CREATE TABLE `Playlist` (
  `playlistid` BIGINT,
  `user` VARCHAR(64),
  `title` VARCHAR(64),
  `desc` VARCHAR(512),
  PRIMARY KEY (`playlistid`),
  FOREIGN KEY (`user`) REFERENCES Users(username)
);

CREATE TABLE `UserSubscribe` (
  `username` VARCHAR(64),
  `playlistid` BIGINT,
  FOREIGN KEY (`username`) REFERENCES Users(username),
  FOREIGN KEY (`playlistid`) REFERENCES Playlist(playlistid)
);

CREATE TABLE `Comments` (
  `commentid` BIGINT,
  `user` VARCHAR(64),
  `video` BIGINT,
  `comment` VARCHAR(64),
  `rating` ENUM("neutral","like","dislike"),
  `time` TIMESTAMP,
  PRIMARY KEY (`commentid`),
  FOREIGN KEY (`user`) REFERENCES Users(username),
  FOREIGN KEY (`video`) REFERENCES Video(videoid)
);

CREATE TABLE `VideoPlaylist` (
  `video` BIGINT,
  `playlist` BIGINT,
  FOREIGN KEY (`video`) REFERENCES Video(videoid),
  FOREIGN KEY (`playlist`) REFERENCES Playlist(playlistid)
);
