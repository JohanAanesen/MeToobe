<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();

$userid = Urge::requireLoggedInUser();
list($playlistTitle,$playlistDesc) = Urge::requireParameterArray('playlist-title', 'playlist-description');

$playlistID = Playlist::create($db, $userid, $playlistTitle, $playlistDesc);


if(isset($_GET['video-id'])){
   header("Location: /php/playlistAdd.php?playlistid=".$playlistID."&videoid=".$_GET['video-id']);
}else{
    header("Location: /playlist?id=".$playlistID);
}