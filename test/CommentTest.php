<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once("vendor/autoload.php");
require_once("classes/DB.php");
require_once("classes/Comment.php");

use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase {

    private $videoid = "1337";
    private $userid  = "8888";

    public function setup() {
        echo "\nSetting up test environment\n";
        ob_flush();
    }

    public function tearDown() {
        echo "\nTearing down test environment\n";
        ob_flush();        
    }

    public function testAddCommentWithCorrectID() {
        $db = DB::getDBConnection();
        $result = Comment::add($db, $userid, $videoid, "TEST TEST TEST TEST !!!!1");
       
        $this->assertNotEquals(0, $result);
    }

    /**
     * @depends testAddComment
     */
    public function testDeleteCommentWithCorrectID() {

    }
}