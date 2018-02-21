<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireUserid(); 
$db     = Urge::requireDatabase();

if (!isset($_GET['videoid'])) {
    Urge::gotoError(400, "Bad request, missing videoid");
}

if( (!isset($_POST['upvote'])) && (!isset($_POST['downvote'])) ) {
    Urge::gotoError(400, "Bad request, you have to either upvote or downvote");
}

$videoid = $_GET['videoid'];

if (isset($_POST['upvote'])){
    Video::videoVote($db, $videoid, $userid, true);
} else if(isset($_POST['downvote'])){
    Video::videoVote($db, $videoid, $userid, false);
}

Urge::gotoVideo($videoid);