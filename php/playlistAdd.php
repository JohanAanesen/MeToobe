<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";
$userid = Urge::requireLoggedInUser();
$db = Urge::requireDatabase();

if(!isset($_GET['playlistid']) || !isset($_GET['videoid'])){
    Urge::gotoHome();
}


$playlistid = $_GET['playlistid'];
$videoid = $_GET['videoid'];


Playlist::pushVideo($db, $playlistid, $videoid);

header('Location: /playlist?id='.$playlistid);
