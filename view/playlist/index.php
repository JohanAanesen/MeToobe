<?php
session_start();
$ROOT = $_SERVER['DOCUMENT_ROOT'];

$playlistID = null;
if(isset($_GET['id'])){
    $playlistID = $_GET['id'];
}

$createMode = false;
$editMode = false;

if(!isset($playlistID)){
    $createMode = true;
}

require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/User.php";
require_once "$ROOT/classes/Video.php";
require_once "$ROOT/classes/Playlist.php";

$db = DB::getDBConnection();
$user = new User($db);
$userid = "";
if($user->loggedIn()) {
    $userid = $_SESSION['userid'];
}
$data = [];
$videos = null;
$playlist = null;

//Not logged in users in createMode get redirected
if($createMode){
    if(!$user->loggedIn())
        header("Location: /");
}

if(!$createMode){
    $playlist = Playlist::get($db, $playlistID);
    $videos = Playlist::getVideos($db, $playlistID, true);
}

if($playlist['userid'] == $userid){
    $editMode = true;
}

if ($user->loggedIn()){
    echo $twig->render('playlist.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'yes',
        'user' => $user->userData,
        'createMode' => $createMode,
        'editMode' => $editMode,
        'playlist' => $playlist,
        'videos' => $videos,
    ));
}else{
    echo $twig->render('playlist.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
        'createMode' => $createMode,
        'playlist' => $playlist,
        'videos' => $videos,
    ));
}

