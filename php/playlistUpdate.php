<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();
$userid = Urge::requireLoggedInUser();
list($playlistTitle, $playlistDesc, $playlistID) = Urge::requireParameterArray(
    'playlist-title',
    'playlist-description',
    'playlist-id');

$val = Playlist::update($db, $playlistID, $playlistTitle, $playlistDesc);

if($val == 1){
    header("Location: /playlist?id=".$playlistID);
}

header("Location: /playlist?id=".$playlistID);