<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();

$userid = Urge::requireLoggedInUser();

$playlistID = Urge::requireParameter('playlist-id');

if (Playlist::subscribePlaylist($db, $userid, $playlistID)){
    header("Location: /playlist?id=".$playlistID);
}else{
    Urge::gotoError(500, "Something went wrong subscribing to playlist");
}


