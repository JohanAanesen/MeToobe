<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;

require_once "./classes/Playlist.php";
require_once "./classes/Video.php";
require_once "./classes/DB.php";

/*
* Contains all three functional tests
*/
class FunctionalTests extends TestCase {

  protected $baseURL    = "http://localhost/view/playlist/index.php";
  protected $accountURL = "http://localhost/user?id=1337ADMIN1337";
  protected $signInURL  = "http://localhost/view/login/index.php";
  protected $session;
  protected $db;
  // Playlist
  private $title       = "Test testing tested";
  private $course      = "IMT2291";
  private $topic       = "Project1";
  private $description = "Programming [Games | Applications] (BPROG)";

  // User
  private $email    = "admin@metoobe.com";
  private $password = "admin";
  private $userID   = "1337ADMIN1337";

  protected function setup() {
    $driver = new \Behat\Mink\Driver\GoutteDriver();
    $this->session = new \Behat\Mink\Session($driver);
    $this->session->start();
  }

  public static function tearDownPlaylist($this1){
    $this1->session->visit($this1->accountURL);
    $page = $this1->session->getPage();

    $idWithURL = $page->find('xpath', '//div[@id="playlistInfo"]/a/@href');
    if($idWithURL != null){
      $id = str_replace('/playlist?id=', '', $idWithURL->getText());
      $this1->db = DB::getDBConnection();
      Playlist::delete($this1->db, $id);
    }else{
      $this1->assertTrue(false, 'No result data found');
    }
  }

  public static function signInUser($this2){
    $this2->session->visit($this2->signInURL);
    $page = $this2->session->getPage();
    $form = $page->find('css', 'form[id="signInUser"]');

    if($form != null){
      $inputEmail    = $form->find('css', 'input[id="inputEmail"]');
      $inputPassword = $form->find('css', 'input[id="inputPassword"]');

      if($inputEmail == null){
        $this2->assertTrue(false, 'Input field: inputEmail not found');
      }else if($inputPassword == null){
        $this2->assertTrue(false, 'Input field: inputPassword not found');
      } else{
        $inputEmail->setValue($this2->email);
        $inputPassword->setValue($this2->password);
        $form->submit();
      }
    } else{
      $this2->assertTrue(false, 'Form not found');
    }
  }

  public static function createAndTestPlaylist($this3){
    $this3->session->visit($this3->baseURL);
    $page = $this3->session->getPage();
    $form = $page->find('css', 'form[id="createPlaylist"]');

    if($form != null){
      $inputTitle       = $form->find('css', 'input[id="playlist-title"]');
      $inputCourse      = $form->find('css', 'input[id="playlist-course"]');
      $inputTopic       = $form->find('css', 'input[id="playlist-topic"]');
      $textareaDescr    = $form->find('css', 'textarea[id="playlist-description"]');

      if($inputTitle == null){
        $this3->assertTrue(false, 'Input field: playlist-title not found');
      }else if($inputCourse == null){
        $this3->assertTrue(false, 'Input field: playlist-course not found');
      }else if($inputTopic == null){
        $this3->assertTrue(false, 'Input field: playlist-topic not found');
      }else if($textareaDescr == null){
        $this3->assertTrue(false, 'Textarea: playlist-description not found');
      }else{
        $inputTitle->setValue($this3->title);
        $inputCourse->setValue($this3->course);
        $inputTopic->setValue($this3->topic);
        $textareaDescr->setValue($this3->description);
        $form->submit();
      }
    } else {
      $this3->assertTrue(false, 'Form: createPlaylist not found');
    }

    // Test if the outcome is right
    $this3->session->visit($this3->accountURL);
    $page = $this3->session->getPage();

    $result = $page->find('xpath', '//div[@id="playlistInfo"]/h2');
    if($result != null){
      $this3->assertEquals($this3->title, $result->getText());
    }else{
      $this3->assertTrue(false, 'No result data found');
    }
  }

  // This and tearDownVideos won't work when videos are required :/
  public static function addThreeVideos($this4){
    $this4->db = DB::getDBConnection();
    $videoId1 = Video::add($this4->db, $this4->userID, 'Test Video 1', 'description1');
    $videoId2 = Video::add($this4->db, $this4->userID, 'Test Video 2', 'description2');
    $videoId3 = Video::add($this4->db, $this4->userID, 'Test Video 3', 'description3');

    $videos = array($videoId1, $videoId2, $videoId3);

    return $videos;
  }


  public static function tearDownVideos($videoArray, $this5){
    $this5->db = DB::getDBConnection();
    for($i = 0; $i < 3; $i++){
      Video::delete($this5->db, $videoArray[$i]);
    }
  }


 /*
 * @depends signInUser
 */
  public function testCreatePlaylist(){
    FunctionalTests::signInUser($this);
    FunctionalTests::createAndTestPlaylist($this);
    FunctionalTests::tearDownPlaylist($this);
  }
/*
  public function testAddThreeVideosToPlaylist(){
    $videoID = FunctionalTests::addThreeVideos($this);



    FunctionalTests::tearDownVideos($videoID, $this);
  }
*/
/*
  public function testChangeOrderOnVideosInPlaylist(){
  }
  */

};
