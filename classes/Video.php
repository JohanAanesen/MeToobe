<?php

/**
  *  class Video
  */
class Video {
    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    function add($uid, $name, $descr, $mime, $size) {
        $sql = "INSERT INTO video (videoid, user, name, descr) VALUES (:videoid, :user, :name, :descr)";
        $sth = $this->db->prepare ($sql);

        $videoid = (string)md5($size . $name . $mime . $uid);
        $sth->bindParam(':videoid', $videoid);
        $sth->bindParam(':user',  $uid);
        $sth->bindParam(':name',  $name);
        $sth->bindParam(':descr', $descr);
        $sth->execute();
        
        if ($sth->rowCount() === 1) {  
            assert($videoid === $this->db->lastInsertId());
            return $videoid;
        }
        return 0;
    }

    function delete($videoid) {
        $sql = "delete from video where id=:videoid";
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':videoid', $videoid);
        $sth->execute();
    }

    /**
      * Saves a video to the filesystem.
      */
    function saveToFile($uid, $videoid, $tmp_filepath, $mime) {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];
        $upload_dir = "$ROOT/uploadedFiles/$uid";
        $extension = "";
        // CREATE DIRECTORY IF DOES NOT EXIST
        if (!file_exists($upload_dir)) { // Brukeren har ikke lastet opp filer tidligere
            @mkdir($upload_dir);
        }

        // Add extension to the video, to know how to play it back.
        if ($mime === "video/quicktime") {
            $extension .= "video.quicktime";
        }
        else {
            $extension .= "video.generic";
        }

        // MOVE FILE FROM TEMP-DIRECTORY to UPLOAD-DIRECTORY
        if (@move_uploaded_file($tmp_filepath, "$upload_dir/$videoid.$extension")) {
            return $uid;
        }
        return 0;
    }
};
