<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();
$userid = Urge::requireLoggedInUser();

$playlistTopic = '';
$playlistCourse = '';

if(isset($_POST['playlist-topic'])){
    $playlistTopic = $_POST['playlist-topic'];
}
if(isset($_POST['playlist-course'])){
    $playlistCourse = $_POST['playlist-course'];
}

list($playlistTitle, $playlistDesc, $playlistID) = Urge::requireParameterArray(
    'playlist-title',
    'playlist-description',
    'playlist-id');

$val = Playlist::update($db, $playlistID, $playlistTitle, $playlistDesc, $playlistCourse, $playlistTopic);

if($val == 1){
    header("Location: /playlist?id=".$playlistID);
}

header("Location: /playlist?id=".$playlistID);