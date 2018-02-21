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

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param userid - user who creates the playlist
     * @param title - title of playlist
     * @param description - description of playlist
     * @return playlist id
     */
    public static function create($db, $userid, $title, $description="") {

        $id = uniqid();
        
        $sql = "INSERT INTO Playlist (id, userid, title, description) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);        
        $param = array($id, $userid, $title, $description);
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
    public static function update($db, $id, $title, $description) {
        $sql = "UPDATE Playlist SET title = ?, description = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $param = array($title, $description, $id);
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

        $sql = 'DELETE FROM VideoPlaylist WHERE playlistid = ?';
        $stmt = $db->prepare($sql);
        $param = array($id);
        
        try {
            $stmt->execute($param);
        } catch (PDOException $e) {
            print_r($e->errorInfo); 
            $db->rollBack(); 
            return;
        }

        $sql = 'DELETE FROM Playlist WHERE id = ?';
        $stmt = $db->prepare($sql);
        $param = array($id);

        try {
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
    public static function removeVideo($db, $id, $videoid) {
        $sql = "DELETE FROM VideoPlaylist WHERE playlistid = ? AND videoid = ?";
        $stmt = $db->prepare($sql);
        $param = array($id, $videoid);
        $stmt->execute($param);
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
    public static function swapVideoRank($db, $id, $videoid, $otherVideoid) {
        
        $db->beginTransaction();

        $sql = "SELECT * FROM VideoPlaylist WHERE videoid = ?";
        $stmt = $db->prepare($sql);
        $param = array($videoid);
        $stmt->execute($param);
        $videoPlaylist = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($videoPlaylist)){
            $db->rollBack();
            return false;
        }

        $sql = "SELECT * FROM VideoPlaylist WHERE videoid = ?";
        $stmt = $db->prepare($sql);
        $param = array($otherVideoid);
        $stmt->execute($param);
        $otherVideoPlaylist = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($otherVideoPlaylist)){
            $db->rollBack();
            return false;
        }

        $sql = "UPDATE VideoPlaylist SET rank = ? WHERE videoid = ?";
        $stmt = $db->prepare($sql);
        $param = array($videoPlaylist['rank'], $otherVideoid);
        $stmt->execute($param);
        if ($stmt->rowCount() !== 1) {
            $db->rollBack();
            return false;
        }

        $sql = "UPDATE VideoPlaylist SET rank = ? WHERE videoid = ?";
        $stmt = $db->prepare($sql);
        $param = array($otherVideoPlaylist['rank'], $videoid);
        $stmt->execute($param);
        if ($stmt->rowCount() !== 1) {
            $db->rollBack();
            return false;
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
            $query = "SELECT id, title FROM playlist WHERE userid = (?) LIMIT 1";
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
}