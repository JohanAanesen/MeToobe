<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser();
list($playlistTitle,$playlistDesc) = Urge::requireParameterArray('playlist-title', 'playlist-description');

$playlistID = Playlist::create($db, $userid, $playlistTitle, $playlistDesc);

header("Location: /playlist?id=".$playlistID);