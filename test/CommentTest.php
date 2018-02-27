<?php

require_once "./vendor/autoload.php";
require_once "./classes/DB.php";

require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./classes/Comment.php";

use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase {

    private $db;

    private $userfullname = "TESTING-TESTING";
    private $useremail    = "test@test.com";
    private $userpassword = "testpassword";
    private $userid       = '';

    private $videoid = '';
    private $video = array(
        'name'       => 'testvideo1', 
        'course'     => 'TDD', 
        'topic'      => 'Testing', 
        'description'=> 'Be a better tester!');

    private $commentidSetup;
    private $commentFromSetup = "Setup test comment TEST TEST TEST !!!!1";
    private $commentidAddDelete;


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
            $this->video['course'],
            $this->video['topic'],
            $this->video['description']);
        $this->assertNotEquals(0, $this->videoid);

        $this->commentidSetup = Comment::add($this->db, $this->userid, $this->videoid, $this->commentFromSetup);
        $this->assertNotEquals(0, $this->commentidSetup);
    }

    public function tearDown() {
        Comment::delete($this->db, $this->commentidSetup);
        User::delete($this->db, $this->userid);
    }  


    public function testGetComment() {
        $comment = Comment::get($this->db, $this->videoid);
        $this->assertNotEmpty($comment);
        
        $comment = $comment[0];
        $this->assertNotEmpty($comment);
        $this->assertArrayHasKey('id', $comment);
        $this->assertArrayHasKey('userid', $comment);
        $this->assertArrayHasKey('videoid', $comment);
        $this->assertArrayHasKey('comment', $comment);
        $this->assertArrayHasKey('time', $comment);

        $this->assertEquals($this->commentFromSetup, $comment['comment']);
    }

    public function testAddComment() {
        $this->commentidAddDelete = Comment::add($this->db, $this->userid, $this->videoid, "Test adding comment TEST TEST TEST!");
        $this->assertNotEquals(0, $this->commentidAddDelete);
    }

    /**
     * @depends testAddComment
     */
    public function testDeleteComment() {       
        $this->commentidAddDelete = Comment::add($this->db, $this->userid, $this->videoid, "Test adding comment TEST TEST TEST!");

        $deleted = Comment::delete($this->db, $this->commentidAddDelete);
        $this->assertNotEquals($deleted, false);
    }
}