<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;

require_once "./classes/DB.php";
require_once "./classes/User.php";
require_once "./classes/Video.php";
require_once "./classes/Playlist.php";

/*
* Contains all three functional tests
*/
class FunctionalTests extends TestCase {

  // URL
  protected $baseURL    = "http://localhost/view/playlist/index.php";
  protected $accountURL = "http://localhost/user?id=1337TEST1337";
  protected $signInURL  = "http://localhost/view/login/index.php";
  protected $videoURL   = "http://localhost/video?id=";
  protected $playlistURL = "http://localhost/playlist?id=";
  protected $session;
  protected $db;

  // Number of videos to be added to playlist
  private $videos = 3;   // Max number is 12

  // Playlist info
  private $playlistID;
  private $title       = "Test testing tested";
  private $course      = "IMT2291";
  private $topic       = "Project1";
  private $description = "Programming [Games | Applications] (BPROG)";
  ////////////////////////////////////////////////////////////////////
  // setupPlaylist
  private $setupPlaylistID;
  private $setupTitle = 'Setup Playlist';
  private $setupDescription = 'This is a description for a playlist';

  // User
  PRIVATE $fullname = "test";
  private $email    = "test@metoobe.com";
  private $password = 'test';
  private $userID   = "1337TEST1337";

  // Videos
  private $arrayVideoID = array();

  protected function setup() {
    $driver = new \Behat\Mink\Driver\GoutteDriver();
    $this->session = new \Behat\Mink\Session($driver);
    $this->session->start();

    // Create test user with the rights of an teacher
    $this->db = DB::getDBConnection();
    $query = "INSERT INTO user (id, fullname, email, password, usertype, wannabe) VALUES (?, ?, ?, ?, ?, ?)";
    $param = array($this->userID, $this->fullname, $this->email, password_hash($this->password, PASSWORD_BCRYPT), 'teacher', 0);
    $stmt = $this->db->prepare($query);
    $stmt->execute($param);

    // Create test playlist
    $this->setupPlaylistID = Playlist::create($this->db, $this->userID, $this->setupTitle, $this->setupDescription);

    // Create test number of '$videos' videos
    for($i = 0; $i < $this->videos; $i++){
      $this->arrayVideoID[] = Video::add($this->db, $this->userID, 'Test-Video-' . ($i + 1), 'Description for Test-Video nr.: ' . ($i + 1));
    }
  }

  protected function tearDown() {
    User::delete($this->db, $this->userID);
  }

  // Signs in to the newly created user
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

  // Creates and tests the playlist
  public static function createAndTestPlaylist($this2){

    // Create
    $this2->session->visit($this2->baseURL);
    $page = $this2->session->getPage();
    $form = $page->find('css', 'form[id="createPlaylist"]');

    if($form != null){
      $inputTitle       = $form->find('css', 'input[id="playlist-title"]');
      $inputCourse      = $form->find('css', 'input[id="playlist-course"]');
      $inputTopic       = $form->find('css', 'input[id="playlist-topic"]');
      $textareaDescr    = $form->find('css', 'textarea[id="playlist-description"]');

      if($inputTitle == null){
        $this2->assertTrue(false, 'Input field: playlist-title not found');
      }else if($inputCourse == null){
        $this2->assertTrue(false, 'Input field: playlist-course not found');
      }else if($inputTopic == null){
        $this2->assertTrue(false, 'Input field: playlist-topic not found');
      }else if($textareaDescr == null){
        $this2->assertTrue(false, 'Textarea: playlist-description not found');
      }else{
        $inputTitle->setValue($this2->title);
        $inputCourse->setValue($this2->course);
        $inputTopic->setValue($this2->topic);
        $textareaDescr->setValue($this2->description);
        $form->submit();
      }
    } else {
      $this2->assertTrue(false, 'Form: createPlaylist not found');
    }

    // Test if the outcome is right
    $this2->session->visit($this2->accountURL);
    $page = $this2->session->getPage();

    // https://devhints.io/xpath#prefixes <- I wasted 3 hours looking for this kind of xpath line :)
    $this2->assertNotNull($page->find('xpath', '//h2[text()="'. $this2->title . '"]'));
  }

  // Adds a number of '$videos' to the playlist and tests if they are all there
  public static function addThreeVideosAndTest($this2){

    // go to user page
    $this2->session->visit($this2->accountURL);
    $page = $this2->session->getPage();

    for($i = 0; $i < $this2->videos; $i++){
      $videoURL = $this2->videoURL . $this2->arrayVideoID[$i];
      $addVideoToPlaylist = 'http://localhost/php/playlistAdd.php?playlistid=' . $this2->setupPlaylistID . '&videoid=' . $this2->arrayVideoID[$i];

      $this2->session->visit($addVideoToPlaylist); // Add video to playlist
      $page = $this2->session->getPage();          //
    }

    // Go to playlist page
    $URL = $this2->playlistURL . $this2->setupPlaylistID;
    $this2->session->visit($URL);
    $page = $this2->session->getPage();

    // Test that all the videos are added
    foreach ($this2->arrayVideoID as $videoID) {
      $this2->assertNotNull($page->find('xpath', '//span[contains(text(), "' . $videoID[$i] .'")]'));
    }
  }

  public function addToPlaylistAndchangeOrder($this2){
    $testVideoID = array();

    // Create two videos
    for($i = 0; $i < 2; $i++){
      $testVideoID[] = Video::add($this2->db, $this2->userID, 'VideoTest' . ($i + 4), 'Description for VideoTest nr.: ' . ($i + 4));
    }

    // Add two videos to playlist
    foreach ($testVideoID as $video) {
      Playlist::pushVideo($this->db, $this->setupPlaylistID, $video);
    }

    // Go to playlist page
    $URL = $this2->playlistURL . $this2->setupPlaylistID;
    $this2->session->visit($URL);
    $page = $this2->session->getPage();

    $this2->assertNotNull($form = $page->find('css', 'form[id="swapDown' . $testVideoID[0] .'"]'));
    $form->submit();

  }


  public function testCreatePlaylist(){
    FunctionalTests::signInUser($this);
    FunctionalTests::createAndTestPlaylist($this);
  }

  public function testAddThreeVideosToPlaylist(){
    FunctionalTests::signInUser($this);
    FunctionalTests::addThreeVideosAndTest($this);
  }
/*
  public function testChangeOrderOnVideosInPlaylist(){
    FunctionalTests::signInUser($this);
    FunctionalTests::addToPlaylistAndchangeOrder($this);
  }
*/

  };
