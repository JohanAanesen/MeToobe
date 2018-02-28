<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db     = Urge::requireDatabase();
$twig   = Urge::requireTwig();
$userid = User::getLoggedInUserid();
$subscribed = false;

if (!isset($_GET['id']) && !$userid) {
    Urge::gotoLogin();
}

if (!isset($_GET['id']) && $userid) {   
    $user = User::get($db, $userid);
    echo $twig->render('playlist.html', array(
        'title' => 'home',
        'userid' => $userid,
        'user' => $user,        
        'createMode' => true,
    ));     
    exit();
}

$playlistID = $_GET['id'];
$playlist   = Playlist::get($db, $playlistID);
$videos     = Playlist::getVideos($db, $playlistID, true);

if (!$userid) {
    echo $twig->render('playlist.html', array(
        'title' => 'home',
        'playlist' => $playlist,
        'videos' => $videos,
    ));
    exit();
}

$editMode = false;
if ($playlist['userid'] === $userid) {
    $editMode = true;
}

$subscribed = Playlist::checkIfSubscribed($db, $userid, $playlistID);

$user = User::get($db, $userid);
echo $twig->render('playlist.html', array(
    'title' => 'home',
    'userid' => $userid,
    'user' => $user,
    'editMode' => $editMode,
    'playlist' => $playlist,
    'videos' => $videos,
    'subscribed' => $subscribed,
));

