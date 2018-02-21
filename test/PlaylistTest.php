<?php

require_once "./vendor/autoload.php";
require_once "./classes/DB.php";
require_once "./classes/Video.php";
require_once "./classes/User.php";
require_once "./classes/Playlist.php";

use PHPUnit\Framework\TestCase;

final class PlaylistTest extends TestCase {

    private $videoid = array('testvideo1', 'testvideo2', 'testvideo3');
    private $userid = '1337TEST1337';
    private $playlistid = 'testplaylist1';
    private $db;

    /* @depends 
     */
    public function setup() {
        $this->db = DB::getDBConnection();

        /*
        $user = new User($this->db);


INSERT INTO `user` (`id`, `fullname`, `email`, `password`, `usertype`, `wannabe`) VALUES ('1337TEST1337', 'testuser', 'test@metoobe.com', '098f6bcd4621d373cade4e832627b4f6', 'student', 0);
INSERT INTO `playlist` (`id`, `userid`,`title`) VALUES ('testplaylist1', '1337TEST1337', 'Test Playlist');  
INSERT INTO `video` (`id`, `userid`, `name`) VALUES ('testvideo1', '1337TEST1337', 'Test Video 1');  
INSERT INTO `video` (`id`, `userid`, `name`) VALUES ('testvideo2', '1337TEST1337', 'TEst Video 2');  
INSERT INTO `video` (`id`, `userid`, `name`) VALUES ('testvideo3', '1337TEST1337', 'Test video 3');

INSERT INTO `VideoPlaylist`(`videoid`, `playlistid`, `rank`)VALUES('testvideo1', 'testplaylist1', 0);
INSERT INTO `VideoPlaylist`(`videoid`, `playlistid`, `rank`)VALUES('testvideo2', 'testplaylist1', 1);
INSERT INTO `VideoPlaylist`(`videoid`, `playlistid`, `rank`)VALUES('testvideo3', 'testplaylist1', 2);
*/
    }

    public function testGetPlaylist() {
        
        $playlist = Playlist::get($this->db, $this->playlistid);
       // print_r($playlist);
        

        $this->assertArrayHasKey('id', $playlist);
        $this->assertArrayHasKey('userid', $playlist);
        $this->assertArrayHasKey('title', $playlist);
        $this->assertArrayHasKey('description', $playlist);

        $this->assertEquals('testplaylist1', $playlist['id']);
        $this->assertEquals('1337TEST1337', $playlist['userid']);

    }

    public function testGetVideos() {
        $videos = Playlist::getVideos($this->db, $this->playlistid);

       // print_r($videos);
        $this->assertEquals(count($this->videoid), count($videos));

        for($i = 0; $i < count($videos); $i += 1 ){
            $v = $videos[$i];
            $vid = $this->videoid[$i];
         //   print_r($v);
            $this->assertEquals($vid, $v['videoid']);
            $this->assertEquals($this->playlistid, $v['playlistid']);
            $this->assertArrayHasKey('id', $v);
            $this->assertArrayHasKey('videoid', $v);
            $this->assertArrayHasKey('playlistid', $v);
        }
    }



    public function testCreateUpdateAndDelete() {
        
        $newPlaylist = array(
            'id' => '',
            'title' => 'New Test Playlist',
            'description' => 'This is a playlist for the big videos'
        );

        //
        // CREATE
        //
        {
            $playlistid = Playlist::create(
                $this->db, 
                $this->userid, 
                $newPlaylist['title'], 
                $newPlaylist['description']);
            
            $this->assertNotEquals(0, $playlistid);

           // print_r('playlistid    '.$playlistid);
            $newPlaylist['id'] = $playlistid;

            $playlist = Playlist::get($this->db, $newPlaylist['id']);
            $this->assertTrue(!empty($playlist));
        }     
        //
        // UPDATE
        //
        {
            $updateDescription = "I am updating this descriptnion now to somehting much more interesting";
            
            $this->assertTrue(Playlist::update(
                $this->db,
                $newPlaylist['id'],
                $newPlaylist['title'],
                $updateDescription
            ));

            $playlist = Playlist::get($this->db, $newPlaylist['id']);
            $this->assertEquals($updateDescription, $playlist['description']);
        }
        //
        // DELETE
        //
        {
            Playlist::delete($this->db, $newPlaylist['id']);

            $playlist = Playlist::get($this->db, $newPlaylist['id']);
            $this->assertTrue(empty($playlist));
        }
    }


    public function testPushRemoveVideoPlaylist() {
        
        $newVideoid = array();
        $newPlaylistid = Playlist::create($this->db, $this->userid, "New playlist");
        $newVideoid[0] = Video::add($this->db, $this->userid, "Newtitle1");
        $newVideoid[1] = Video::add($this->db, $this->userid, "Newtitle2");
        $newVideoid[2] = Video::add($this->db, $this->userid, "Newtitle3");
        
        $this->assertNotEquals(0, $newVideoid[0]);
        $this->assertNotEquals(0, $newVideoid[1]);
        $this->assertNotEquals(0, $newVideoid[2]);
        //
        // PUSH
        //
        foreach ($newVideoid as $videoid) {    
            $videoPlaylistId = Playlist::pushVideo(
                $this->db,
                $newPlaylistid,
                $videoid
            );
            $this->assertNotEquals(0, $videoPlaylistId);
        }

        // Test to push the first videoid again.
        try {
            $videoPlaylistId = Playlist::pushVideo(
                $this->db,
                $newPlaylistid,
                $videoid[0]
            );
            $this->assertTrue(false);
            return;
        } catch (PDOException $e) {
            $this->assertEquals($e->errorInfo[2], 'Cannot add or update a child row: a foreign key constraint fails (`urgedb`.`videoplaylist`, CONSTRAINT `videoplaylist_ibfk_1` FOREIGN KEY (`videoid`) REFERENCES `Video` (`id`))');
        }

        Playlist::delete($this->db, $newPlaylistid);
        Video::delete($this->db, $newVideoid[0]);
        Video::delete($this->db, $newVideoid[1]);
        Video::delete($this->db, $newVideoid[2]);
    }

    /*
     * @depends testGetVideos
     */
    public function testSwapVideos() {

        $videos = Playlist::getVideos($this->db, $this->playlistid);

        $this->assertArrayHasKey(0,$videos);
        $this->assertArrayHasKey(1,$videos);
        $this->assertArrayHasKey('videoid',$videos[0]);
        $this->assertArrayHasKey('rank', $videos[0]);

        $videoID     = $videos[0]['videoid'];
        $otherVideoID = $videos[1]['videoid'];

        $videoRankBefore      = $videos[0]['rank'];
        $otherVideoRankBefore = $videos[1]['rank'];

        $this->assertEquals($this->videoid[0], $videoID);
        $this->assertEquals($this->videoid[1], $otherVideoID);

        //
        // SWAP FORWARD
        //
        $updateCount = Playlist::swapVideoRank(
            $this->db, 
            $this->playlistid,
            $videoID,
            $otherVideoID
        );
        //$this->assertEquals(2, $updateCount);

        $videosAfter = Playlist::getVideos($this->db, $this->playlistid);     

        $videoRankAfter      = $videosAfter[0]['rank'];
        $otherVideoRankAfter = $videosAfter[1]['rank'];
        
        $this->assertEquals($videoRankBefore, $otherVideoRankAfter);
        $this->assertEquals($videoRankAfter,  $otherVideoRankBefore);

        //
        // SWAP BACK
        //
        $updateCount = Playlist::swapVideoRank(
            $this->db, 
            $this->playlistid,
            $videoID,
            $otherVideoID
        );

        $videos = Playlist::getVideos($this->db, $this->playlistid);     
        
        $videoRankBack      = $videos[0]['rank'];
        $otherVideoRankBack = $videos[1]['rank'];
        
        $this->assertEquals($videoRankBefore, $videoRankBack);
        $this->assertEquals($otherVideoRankBack, $otherVideoRankBefore);
    }
};
