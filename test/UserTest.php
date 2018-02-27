<?php

require_once "./vendor/autoload.php";
require_once "./classes/DB.php";
require_once "./classes/User.php";

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {

    private $db;

    private $userfullname = "TESTING-TESTING-USER";
    private $useremail    = "test@test.com";
    private $userpassword = "testpassword";
    private $userid       = '';

    private $new_userfullname = "NEW-TESTING-USER";
    private $new_useremail    = "new@test.com";
    private $new_userpassword = "newtestpassword";
    private $new_userid       = '';


    public function setup() {
        $this->db     = DB::getDBConnection();
        $this->userid = User::create(
            $this->db,
            $this->userfullname,
            $this->useremail,
            $this->userpassword,
            false);
        $this->assertNotEquals(0, $this->userid);
    }

    public function tearDown() {
        User::delete($this->db, $this->new_userid);
        User::delete($this->db, $this->userid);
    }  

    public function testCreateUser() {

        $this->new_userid = User::create(
            $this->db, 
            $this->new_userfullname, 
            $this->new_useremail,
            $this->new_userpassword,
            false);

        $this->assertNotEquals(0, $this->new_userid);
    }

    /*
     * @depends testCreateUser
     */
    public function testDeleteUser() {
        $this->new_userid = User::create(
            $this->db, 
            $this->new_userfullname, 
            $this->new_useremail,
            $this->new_userpassword,
            false);

        $deleted = User::delete($this->db, $this->new_userid);
        $this->assertTrue($deleted);
    }

    public function testLogin() {
        $loggedInUserid = User::login($this->db, $this->useremail, $this->userpassword);
        $this->assertNotEquals(0, $loggedInUserid);
        $this->assertEquals($this->userid, $loggedInUserid);
        $this->assertEquals(User::getLoggedInUserId(), $loggedInUserid);
    }

    /*
     * @depends testLogin()
     */
    public function testLogout() {
        User::logout();
        $this->assertEquals(0, User::getLoggedInUserId());
    }
    /*
     * @depends testLogin()
     * @depends testLogout()
     */
    public function testUpdatePasswordLoginAndLogout() {

        $this->userpassword = "secretesuperpass";
        $updated = User::updateUser(
            $this->db,
            $this->userid,
            $this->userpassword,
            'student',
            false);

        $this->assertNotEquals(0, $updated);

        $loggedInUserid = User::login($this->db, $this->useremail, $this->userpassword);
        $this->assertNotEquals(0, $loggedInUserid);
        $this->assertEquals($this->userid, $loggedInUserid);
        $this->assertEquals(User::getLoggedInUserId(), $loggedInUserid);
        
        User::logout();
        $this->assertEquals(0, User::getLoggedInUserId());
    }


    public function testGetUser() {
        $user = User::get($this->db, $this->userid);

        $this->assertNotEmpty($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('fullname', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayHasKey('password', $user);
        $this->assertArrayHasKey('usertype', $user);
        $this->assertArrayHasKey('wannabe', $user);
    }
}

?>
