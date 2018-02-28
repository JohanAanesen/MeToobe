<?php

class Playlist {

    /* 
     * @param db - PDO connection object
     * @param id - playlist id
     * @return playlist - single playlist
     */
    public static function get($db, $id) {
        $sql = "SELECT * FROM Playlist WHERE id=?";  
        $stmt = $db->prepare($sql);
        $param = array($id);
        $stmt->execute($param);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /* 
     * @param db - PDO connection object
     * @param id - playlist id
     * @return videos - array of videos connected to the playlist id
     */
    public static function getVideos($db, $id, $orderByRank=false) {
        $sql = "SELECT * FROM VideoPlaylist
                INNER JOIN video ON videoplaylist.videoid = video.id
                WHERE playlistid=?";
        if ($orderByRank === true) {
            $sql .= " ORDER BY rank";
        }

        $stmt = $db->prepare($sql);
        $param = array($id);
        $stmt->execute($param);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $db
     * @return array|null
     */
    public static function getNewPlaylists($db){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT id, title, thumbnail FROM playlist LIMIT 6";
            $param = array();
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param userid - user who creates the playlist
     * @param title - title of playlist
     * @param description - description of playlist
     * @return playlist id
     */
    public static function create($db, $userid, $title, $description="", $course="", $topic="", $thumbnail="") {

        $id = uniqid();
        
        $sql = "INSERT INTO Playlist (id, userid, title, description, course, topic, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);        
        $param = array($id, $userid, $title, $description, $course, $topic, $thumbnail);
        $stmt->execute($param);

        if ($stmt->rowCount() !== 1) {
            return 0;
        }
        return $id;
    }

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param id - id of playlist
     * @param title - title of playlist
     * @param description - description of playlist
     */
    public static function update($db, $id, $title, $description, $course, $topic) {
        $sql = "UPDATE Playlist SET title = ?, description = ?, course = ?, topic = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $param = array($title, $description, $course, $topic, $id);
        $stmt->execute($param);

        return ($stmt->rowCount() === 1);
    }
    /* 
     * @requires login
     * @param db - PDO connection object
     * @param id - playlist id
     */
    public static function delete($db, $id) {

        $db->beginTransaction();

        try {
        
        $sql = 'DELETE FROM VideoPlaylist WHERE playlistid = ?';
        $stmt = $db->prepare($sql);
        $param = array($id);
        $stmt->execute($param);


        $sql = 'DELETE FROM Playlist WHERE id = ?';
        $stmt = $db->prepare($sql);
        $param = array($id);
        $stmt->execute($param);
        
        } catch (PDOException $e) {
            print_r($e->errorInfo); 
            $db->rollBack(); 
            return;
        }
            
        $db->commit();
    }
    

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param id - playlist id
     * @param videoid - id of video of which we want to append
     * @throws PDOException 
     */
    public static function pushVideo($db, $id, $videoid) {
        $sql = "INSERT INTO VideoPlaylist (playlistid, videoid, rank) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $param = array($id, $videoid, count(Playlist::getVideos($db, $id)) );
        $stmt->execute($param);

        if ($stmt->rowCount() !== 1) {
            return 0;
        }
        return $db->lastInsertId();
    }

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param id - playlist id
     * @param videoid - video 
     * @throws PDOException 
     */
    public static function removeVideo($db, $playlistid, $videoid, $rank) {
        $db->beginTransaction();

        try{
            $sql = "DELETE FROM VideoPlaylist WHERE playlistid = ? AND videoid = ? AND rank = ?";
            $stmt = $db->prepare($sql);
            $param = array($playlistid, $videoid, $rank);
            $stmt->execute($param);
        }catch (PDOException $e){
            print_r($e);
            $db->rollBack();
            return;
        }

        $db->commit();
        return;
    }

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param id - playlist id
     * @param videoid - video we want to swap
     * @param othervideoid - video we want swap with
     * @return true if 2 rows was updated, false otherwise
     * @doc https://stackoverflow.com/questions/2810606/sql-swap-primary-key-values - 11.02.18
     * @throws PDOException     
     */
    public static function swapVideoRank($db, $playlistid, $videoid, $otherVideoid, $rank, $otherRank) {
        
        $db->beginTransaction();

        try {

            $sql = "UPDATE VideoPlaylist SET rank = ? WHERE videoid = ? AND playlistid = ? AND rank = ?";
            $stmt = $db->prepare($sql);
            $param = array($rank, $otherVideoid, $playlistid, $otherRank);
            $stmt->execute($param);

        } catch (PDOException $e) {
            print_r($e);
            $db->rollBack();
            return;
        }

        try{

            $sql = "UPDATE VideoPlaylist SET rank = ? WHERE videoid = ? AND playlistid = ? AND rank = ?";
            $stmt = $db->prepare($sql);
            $param = array($otherRank, $videoid, $playlistid, $rank);
            $stmt->execute($param);

        } catch (PDOException $e) {
            print_r($e);
            $db->rollBack();
            return;
        }

        $db->commit();

        return true;
    }

    /**
     * @param $db
     * @param $userid
     * @return array|null
     */
    public static function getUserPlaylist($db, $userid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT id, title, thumbnail FROM playlist WHERE userid = (?)";
            $param = array($userid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                $playlists = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $playlists[] = $row;
                }
                return $playlists;
            }
        }catch(PDOException $ex){
            echo "Can't get playlists. Something went wrong!"; //Error message
        }
        return null;
    }

    /**
     * @param $db
     * @param $playlistid
     * @param $videoRank
     * @return int|null
     */
    public static function updateVideoRanks($db, $playlistid, $videoid, $videoRank){
        $currentRank = null;

        $playlistLength = self::getPlaylistLength($db, $playlistid);

        //if video is the only one or is at the end of the 'array', no swap needed.
        if($videoRank == $playlistLength-1){
            return $videoRank;
        }

        $currentRank = $videoRank;
        //iterates from the current rank towards the end of playlist and swaps ranks
        if($videoRank < $playlistLength-1) {
            for ($i = $videoRank; $i < $playlistLength; $i++) {
                $nextVideoID = self::getVideoIdByRankPlaylist($db, $i+1, $playlistid);
                echo $i."-";
                if($videoid != $nextVideoID) {
                    if(self::swapVideoRank($db, $playlistid, $videoid, $nextVideoID, $i, $i+1)){
                        echo "success";
                    }
                }
                $currentRank = $i+1;
            }
        }

        return $currentRank;
    }

    /**
     * @param $db
     * @param $playlistid
     * @return null
     */
    public static function getPlaylistLength($db, $playlistid){
        try{
            //retrieves the "length" of playlist
            $sql = "SELECT COUNT(videoid) AS antall FROM VideoPlaylist WHERE playlistid = ?";
            $stmt = $db->prepare($sql);
            $param = array($playlistid);
            $stmt->execute($param);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if(isset($row['antall'])){
                return $row['antall'];
            }


        } catch (PDOException $e) {
            print_r($e);
            return null;
        }
        return null;
    }

    /**
     * @param $db
     * @param $rank
     * @param $playlistid
     * @return null|string
     */
    public static function getVideoIdByRankPlaylist($db, $rank, $playlistid){
        try{
            $sql = "SELECT videoid FROM VideoPlaylist WHERE playlistid = ? AND rank = ? LIMIT 1";
            $stmt = $db->prepare($sql);
            $param = array($playlistid, $rank);
            $stmt->execute($param);
            if ($stmt->rowCount()==1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['videoid'];
            }
        } catch (PDOException $e) {
            print_r($e->errorInfo);
            return null;
        }
        return null;
    }

    /**
     * @param $db
     * @param $q
     * @return array|null
     */
    public static function searchPlaylist($db, $q){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT playlist.id, playlist.title, playlist.description FROM playlist
                  INNER JOIN user ON playlist.userid = user.id
                  WHERE playlist.title LIKE (?)
                  OR user.fullname LIKE (?)
                  OR user.email LIKE (?)
                  OR playlist.description LIKE (?)
                  OR playlist.course LIKE (?)
                  OR playlist.topic LIKE (?)
                  LIMIT 10";

            //adding the wildcard characters to query word
            $qWild = "%".$q."%";

            $param = array($qWild, $qWild, $qWild, $qWild, $qWild, $qWild);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                $videos = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $videos[] = $row;
                }
                return $videos;
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

    public static function subscribePlaylist($db, $userid, $playlistid){
        $sql = "INSERT INTO usersubscribe (userid, playlistid) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        $param = array($userid, $playlistid);
        $stmt->execute($param);

        if ($stmt->rowCount() !== 1) {
            return false;
        }
        return true;
    }

    public static function unsubscribePlaylist($db, $userid, $playlistid){
       try{
        $sql = "DELETE FROM usersubscribe WHERE userid = ? AND playlistid = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $param = array($userid, $playlistid);
        $stmt->execute($param);
        }catch (PDOException $e){
            print_r($e);
            return false;
        }
        return true;
    }

    public static function checkIfSubscribed($db, $userid, $playlistid){
        $sql = "SELECT * FROM usersubscribe WHERE userid = ? AND playlistid = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $param = array($userid, $playlistid);
        $stmt->execute($param);

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }

}