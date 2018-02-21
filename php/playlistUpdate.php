<?php
session_start();
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/php/requirelogin.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/Playlist.php";
require_once "$ROOT/classes/User.php";


$db = DB::getDBConnection();
$user = new User($db);
$userid = "";
if($user->loggedIn()) {
    $userid = $_SESSION['userid'];
}


$playlistTitle = $_POST['playlist-title'];
$playlistDesc = $_POST['playlist-description'];
$playlistID = $_POST['playlist-id'];

$val = Playlist::update($db, $playlistID, $playlistTitle, $playlistDesc);

if($val == 1){
    header("Location: /playlist?id=".$playlistID);
}

header("Location: /playlist?id=".$playlistID);