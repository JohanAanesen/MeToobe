<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";
$userid = Urge::requireLoggedInUser();
$db = Urge::requireDatabase();

$playlistid = null;

if(isset($_POST['playlist-id'])){
    $playlistid = $_POST['playlist-id'];
}

Playlist::delete($db, $playlistid);

header('Location: /playlist');
