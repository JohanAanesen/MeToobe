<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;

require_once "./classes/Video.php";
require_once "./classes/DB.php";

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
  private $videos = 3;

  // Playlist info
  private $title       = "Test testing tested";
  private $course      = "IMT2291";
  private $topic       = "Project1";
  private $description = "Programming [Games | Applications] (BPROG)";

  // User
  PRIVATE $fullname = "test";
  private $email    = "test@metoobe.com";
  private $password = 'test';
  private $userID   = "1337TEST1337";

  protected function setup() {
    $driver = new \Behat\Mink\Driver\GoutteDriver();
    $this->session = new \Behat\Mink\Session($driver);
    $this->session->start();
  }

  // Creates user in db
  public static function createUserOnce($this0){
    $db = DB::getDBConnection();
    $query = "INSERT INTO user (id, fullname, email, password, usertype, wannabe) VALUES (?, ?, ?, ?, ?, ?)";
    $param = array($this0->userID, $this0->fullname, $this0->email, password_hash($this0->password, PASSWORD_BCRYPT), 'teacher', 0);
    $stmt = $db->prepare($query);
    $stmt->execute($param);
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
  public static function createAndTestPlaylist($this3){

    // Create
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

  // Adds a number of '$videos' to the db
  public static function createThreeVideos($this4){
    $this4->db = DB::getDBConnection();
    for($i = 0; $i < $this4->videos; $i++){
      $arrayVideo[] = Video::add($this4->db, $this4->userID, 'Test-Video-' . ($i + 1), 'Description for Test-Video nr.: ' . ($i + 1));
    }
    return $arrayVideo;
  }

  // Tear down the user from the account user-page
  public static function tearDownUser($this5){
    $this5->session->visit($this5->accountURL);
    $page = $this5->session->getPage();

    $form = $page->find('css', 'form[id="deleteUser"]');
    if($form!=null){
      $inputCheckBox = $form->find('css', 'input[id="areUsure"]');
      if($inputCheckBox != null){
        $inputCheckBox->setValue('yes');
        $form->submit();
      } else{
        $this5->assertTrue(false, 'Input field: areUsure not found');
      }
    } else{
      $this5->assertTrue(false, 'Form: deleteUser not found');
    }
  }

  // Adds a number of '$videos' to the playlist and tests if they are all there
  public static function addThreeVideosAndTest($this6, $idVideo){

    $this6->session->visit($this6->accountURL);
    $page = $this6->session->getPage();

    $href = $page->find('xpath', '//div[@id="playlistInfo"]/a/@href');
    if($href != null){
      $idPlaylist = str_replace('/playlist?id=', '', $href->getText());
      for($i = 0; $i < $this6->videos; $i++){
        $videoURL = $this6->videoURL . $idVideo[$i];
        $addVideoToPlaylist = 'http://localhost/php/playlistAdd.php?playlistid=' . $idPlaylist . '&videoid=' . $idVideo[$i];

        $this6->session->visit($addVideoToPlaylist); // Add video to playlist
        $page = $this6->session->getPage();          //
      }
    }
    else {
      $this6->assertTrue(false, 'href not found');
    }

    $URL = $this6->playlistURL . $idPlaylist;

    $this6->session->visit($URL);
    $page = $this6->session->getPage();

    $allH2s = $page->findAll('xpath', '//h2');
    $i = 1;
    foreach ($allH2s as $h2) {
      if($h2 != null){
        $videoName = 'Test-Video-' . $i++;
        $this6->assertEquals($videoName, $h2->getText());
      }
    }
  }

 /*
 * @depends createUserOnce
 * @depends signInUser
 */
  public function testCreatePlaylist(){
    FunctionalTests::createUserOnce($this);

    FunctionalTests::signInUser($this);
    FunctionalTests::createAndTestPlaylist($this);
  }

  /*
  * @depends createUserOnce
  * @depends createAndTestPlaylist
  */
  public function testAddThreeVideosToPlaylist(){
    $videoID = FunctionalTests::createThreeVideos($this);
    FunctionalTests::signInUser($this);
    FunctionalTests::addThreeVideosAndTest($this, $videoID);

    FunctionalTests::tearDownUser($this);
  }

  /*
  * @depends createUserOnce
  * @depends createAndTestPlaylist
  * @depends createThreeVideos
  * @depends addThreeVideosAndTest
  */
/*public function testChangeOrderOnVideosInPlaylist(){
  }
*/

};
