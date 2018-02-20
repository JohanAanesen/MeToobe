<?php
session_start();

$ROOT    = $_SERVER['DOCUMENT_ROOT'];
$userPageId = $_GET['id'];

//if videoid is not set, return user to frontpage.
if (!isset($userPageId)){
    header("Location: /");
    exit();
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
$userVideos = Video::getUsersVideos($db, $userPageId);
if(!isset($userVideos)){
    $userVideos = "no";
}
$userStats = User::getUserStats($db, $userPageId);
if(!isset($userStats)){
    $userStats = "no";
}
$userPlaylists = Playlist::getUserPlaylist($db, $userPageId);
if(!isset($userPlaylists)){
    $userPlaylists = "no";
}


if ($user->loggedIn()){
    echo $twig->render('user.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'yes',
        'user' => $user->userData,
        'userVideos' => $userVideos,
        'userStats' => $userStats,
        'userPlaylists' => $userPlaylists,
    ));
}else{
    echo $twig->render('user.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
        'userVideos' => $userVideos,
        'userStats' => $userStats,
        'userPlaylists' => $userPlaylists,
    ));
}