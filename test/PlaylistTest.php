<?php

require_once "./vendor/autoload.php";
require_once "./classes/DB.php";
require_once "./classes/Playlist.php";

use PHPUnit\Framework\TestCase;

final class PlaylistTest extends TestCase {

    private $videoid = array('testvideo1', 'testvideo2', 'testvideo3');
    private $userid = '1337TEST1337';
    private $playlistid = 'testplaylist1';
    private $db;

    public function setup() {
        $this->db = DB::getDBConnection();
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
            $this->assertTrue($v['id'] > 0, $v['id']." > 0");         
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
};
