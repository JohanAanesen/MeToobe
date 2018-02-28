<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\NodeElement;

require_once "./classes/Playlist.php";
require_once "./classes/DB.php";

/*
* Contains all three functional tests
*/
class FunctionalTests extends TestCase {

  protected $baseURL = "http://localhost/view/playlist/index.php";
  protected $accountURL = "http://localhost/user?id=1337ADMIN1337";
  protected $signInURL = "http://localhost/view/login/index.php";
  protected $session;
  protected $db;

  // Playlist
  private $title  = "Test testing tested";
  private $course = "IMT2291";
  private $topic  = "Project1";
  private $description = "Programming [Games | Applications] (BPROG)";

  // User
  private $email    = "admin@metoobe.com";
  private $password = "admin";

  protected function setup() {
    $driver = new \Behat\Mink\Driver\GoutteDriver();
    $this->session = new \Behat\Mink\Session($driver);
    $this->session->start();
  }


  // Can not do testing of playlist without user
  public function testSignInUserAndCreatePlaylist(){

    // Sign in
    $this->session->visit($this->signInURL);
    $page = $this->session->getPage();
    $form = $page->find('css', 'form[id="signInUser"]');

    if($form != null){
      $inputEmail    = $form->find('css', 'input[id="inputEmail"]');
      $inputPassword = $form->find('css', 'input[id="inputPassword"]');

      if($inputEmail == null){
        $this->assertTrue(false, 'Input field: inputEmail not found');
      }else if($inputPassword == null){
        $this->assertTrue(false, 'Input field: inputPassword not found');
      } else{
        $inputEmail->setValue($this->email);
        $inputPassword->setValue($this->password);
        $form->submit();
      }
    } else{
      $this->assertTrue(false, 'Form not found');
    }


    // Create playlist
    $this->session->visit($this->baseURL);
    $page = $this->session->getPage();
    $form = $page->find('css', 'form[id="createPlaylist"]');

    if($form != null){
      $inputTitle       = $form->find('css', 'input[id="playlist-title"]');
      $inputCourse      = $form->find('css', 'input[id="playlist-course"]');
      $inputTopic       = $form->find('css', 'input[id="playlist-topic"]');
      $textareaDescr    = $form->find('css', 'textarea[id="playlist-description"]');

      if($inputTitle == null){
        $this->assertTrue(false, 'Input field: playlist-title not found');
      }else if($inputCourse == null){
        $this->assertTrue(false, 'Input field: playlist-course not found');
      }else if($inputTopic == null){
        $this->assertTrue(false, 'Input field: playlist-topic not found');
      }else if($textareaDescr == null){
        $this->assertTrue(false, 'Textarea: playlist-description not found');
      }else{
        $inputTitle->setValue($this->title);
        $inputCourse->setValue($this->course);
        $inputTopic->setValue($this->topic);
        $textareaDescr->setValue($this->description);
        $form->submit();
      }
    } else {
      $this->assertTrue(false, 'Form: createPlaylist not found');
    }

    // Test if the outcome is right
    $this->session->visit($this->accountURL);
    $page = $this->session->getPage();

    $result = $page->find('xpath', '//div[@id="playlistInfo"]/h2');
    if($result != null){
      $this->assertEquals($this->title, $result->getText());
    }else{
      $this->assertTrue(false, 'No result data found');
    }

  FunctionalTests::tearDownPlaylist($this);
  }
/*
  public function testAddThreeVideosToPlaylist(){

  }

  public function testChangeOrderOnVideosInPlaylist(){
  }
  */

  // This should not be a test, but I don't know how else to do it and it's 03:10 now
  public static function tearDownPlaylist($this2){
    $this2->session->visit($this2->accountURL);
    $page = $this2->session->getPage();

    $idWithURL = $page->find('xpath', '//div[@id="playlistInfo"]/a/@href');
    if($idWithURL != null){
      $id = str_replace('/playlist?id=', '', $idWithURL->getText());
      $this2->db = DB::getDBConnection();
      Playlist::delete($this2->db, $id);
    }else{
      $this2->assertTrue(false, 'No result data found');
    }
  }

};
