<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser(); 
$db     = Urge::requireDatabase();

if (!isset($_GET['videoid'])) {
    Urge::gotoError(400, "Bad request, missing videoid");
}

$videoid = $_GET['videoid'];

if(isset($_POST['revote'])){
    $vote = $_POST['revote'];
    if($vote == 'up' || $vote == 'down'){
        if(Video::updateLike($db, $videoid, $userid, $vote)){
            Urge::gotoVideo($videoid);
            exit();
        }
    }else{
        Urge::gotoError(400, "Bad request");
        exit();
    }
}

if( (!isset($_POST['upvote'])) && (!isset($_POST['downvote'])) ) {
    Urge::gotoError(400, "Bad request, you have to either upvote or downvote");
}

if (isset($_POST['upvote'])){
    Video::videoVote($db, $videoid, $userid, true);
} else if(isset($_POST['downvote'])){
    Video::videoVote($db, $videoid, $userid, false);
}

Urge::gotoVideo($videoid);