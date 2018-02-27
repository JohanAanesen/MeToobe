<?php

require_once "./vendor/autoload.php";
require_once "./classes/DB.php";
require_once "./classes/Video.php";
require_once "./classes/User.php";
require_once "./classes/Playlist.php";

use PHPUnit\Framework\TestCase;

final class PlaylistTest extends TestCase {

    private $userfullname = "TESTING-TESTING";
    private $useremail    = "test@test.com";
    private $userpassword = "testpassword";
    private $userid       = '';

    private $playlistid = '';
    private $playlisttitle = 'Test Playlist';
    private $playlistdescription = 'This is a test playlist';

    private $videoArray = array(
        array('name'=> 'testvideo1','description'=> 'Be a better tester!'),
        array('name'=> 'testvideo2','description'=> 'Be a better tester!'),
        array('name'=> 'testvideo3','description'=> 'Be a better tester!')
    );

    private $videoidArray = array(); 
    private $db;

    public function setUp() {
        $this->db     = DB::getDBConnection();

        // 1. Add test user
        $this->userid = User::create(
            $this->db,
            $this->userfullname,
            $this->useremail,
            $this->userpassword,
            false);

        $this->assertNotEquals($this->userid, 0);

        // 2. Add test playlist connected to the test user
        $this->playlistid = Playlist::create(
            $this->db, 
            $this->userid,
            $this->playlisttitle,
            $this->playlistdescription);


        $this->assertNotEquals($this->playlistid, 0);


        // 3. Add test video connected to the test user
        foreach ($this->videoArray as $video) {
            $vid = Video::add(
                $this->db,
                $this->userid,
                $video['name'],
                $video['description']);

            $this->assertNotEquals($vid, 0);
            array_push($this->videoidArray, $vid);

            // 4. Add PlaylistVideo connected to video, playlist and user.

            $playlistVideoid = Playlist::pushVideo(
                $this->db,
                $this->playlistid,
                $vid);

            $this->assertNotEquals($playlistVideoid, 0);
        }


    }

    public function tearDown() {
        Playlist::delete($this->db, $this->playlistid);
        User::delete($this->db,     $this->userid);
    }


    public function testGetPlaylist() {
        
        $playlist = Playlist::get($this->db, $this->playlistid);
       
        $this->assertArrayHasKey('id', $playlist);
        $this->assertArrayHasKey('userid', $playlist);
        $this->assertArrayHasKey('title', $playlist);
        $this->assertArrayHasKey('description', $playlist);

        $this->assertEquals($this->playlistid, $playlist['id']);
        $this->assertEquals($this->userid, $playlist['userid']);
    }


    public function testGetPlaylistVideos() {
        $videos = Playlist::getVideos($this->db, $this->playlistid);
        $this->assertEquals(count($this->videoidArray), count($videos));

        for($i = 0; $i < count($videos); $i += 1) {
            $v = $videos[$i];
            $vid = $this->videoidArray[$i];

            $this->assertEquals($vid, $v['videoid']);
            $this->assertEquals($this->playlistid, $v['playlistid']);
            $this->assertArrayHasKey('id', $v);
            $this->assertArrayHasKey('videoid', $v);
            $this->assertArrayHasKey('playlistid', $v);
        }
    }

    public function testUpdate() {

        $updateDescription = "I am updating this descriptnion now to somehting much more interesting";
        
        $this->assertTrue(Playlist::update(
            $this->db,
            $this->playlistid,
            $this->playlisttitle,
            $updateDescription
        ));

        $playlist = Playlist::get($this->db, $this->playlistid);
        $this->assertEquals($updateDescription, $playlist['description']);
    }

    public function testCreateAndDelete() {
        
        $newPlaylist = array(
            'id' => '',
            'title' => 'New Test Playlist',
            'description' => 'This is a playlist for the big videos'
        );

        //
        // 1. CREATE
        //
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

        //
        // 2. DELETE
        //
        Playlist::delete($this->db, $newPlaylist['id']);

        $playlist = Playlist::get($this->db, $newPlaylist['id']);
        $this->assertTrue(empty($playlist));
    }

   public function testPushNewPlaylistVideo() {
        
        $newVideoid = Video::add(
            $this->db, 
            $this->userid, 
            "NewVideo", 
            "Adding an extra video");

        $this->assertNotEquals(0, $newVideoid);
        
        $newPlaylistVideoid = Playlist::pushVideo(
            $this->db,
            $this->playlistid,
            $newVideoid
        );
        $this->assertNotEquals(0, $newPlaylistVideoid);
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

        $this->assertEquals($this->videoidArray[0], $videoID);
        $this->assertEquals($this->videoidArray[1], $otherVideoID);

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
