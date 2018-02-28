<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser();
$db     = Urge::requireDatabase();


list($playlistid, $videoid, $videoRank) = Urge::requireParameterArray('playlist-id','video-id','video-rank');