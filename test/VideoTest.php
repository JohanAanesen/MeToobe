<?php

require_once "./vendor/autoload.php";
require_once "./classes/DB.php";
require_once "./classes/Video.php";
require_once "./classes/User.php";

use PHPUnit\Framework\TestCase;

final class VideoTest extends TestCase {


    private $db;

    private $userfullname = "TESTING-TESTING";
    private $useremail    = "test@test.com";
    private $userpassword = "testpassword";
    private $userid       = '';

    private $videoid = '';
    private $video = array(
        'name'       => 'testvideo1', 
        'description'=> 'Be a better tester!');

    private $new_videoid = '';
    private $new_video = array(
        'name'       => 'testvideo1', 
        'description'=> 'Be a better tester!');


    public function setup() {
        $this->db     = DB::getDBConnection();
        $this->userid = User::create(
            $this->db,
            $this->userfullname,
            $this->useremail,
            $this->userpassword,
            false);
        $this->assertNotEquals($this->userid, 0);
 
        $this->videoid = Video::add(
            $this->db,
            $this->userid,
            $this->video['name'],
            $this->video['description']);
        $this->assertNotEquals(0, $this->videoid);

    }

    public function tearDown() {
        User::delete($this->db, $this->userid);
    }  

    public function testGet()    {
        $video = Video::get($this->db, $this->videoid);
        $this->assertNotEmpty($video);
        $this->assertArrayHasKey('id', $video);
        $this->assertArrayHasKey('userid', $video);
        $this->assertArrayHasKey('name', $video);
        $this->assertArrayHasKey('description', $video);
        $this->assertArrayHasKey('mime', $video);
        $this->assertEquals($this->videoid, $video['id']);
    } 
    
    public function testAdd()    {
        $this->new_videoid = Video::add(
            $this->db,
            $this->userid,
            $this->new_video['name'],
            $this->new_video['description']);
        
        $this->assertNotEquals(0, $this->new_videoid);        
    }

    /*
     * @depends testAdd
     */
    public function testDelete() {
        $this->new_videoid = Video::add(
            $this->db,
            $this->userid,
            $this->new_video['name'],
            $this->new_video['description']);

        $deleted = Video::delete($this->db, $this->new_videoid);
        $this->assertTrue($deleted);
    }

    public function testIfUploadFolderExistsAndIsReadableWritable() {
        $pathToFolder = './uploadedFiles';
        $this->assertTrue(file_exists($pathToFolder));
        $this->assertFileExists($pathToFolder);
        $this->assertFileIsReadable($pathToFolder);
        $this->assertFileIsWritable($pathToFolder);
    }   
}

?>
