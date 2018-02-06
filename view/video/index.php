<?php
session_start();

$ROOT    = $_SERVER['DOCUMENT_ROOT'];
$videoid = $_GET['id'];

//if videoid is not set, return user to frontpage.
if (!isset($videoid)){
    header("Location: /");
    exit();
}

require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/user.php";
require_once "$ROOT/classes/Video.php";
require_once "$ROOT/classes/Comment.php";

$db = DB::getDBConnection();
$user = new User($db);
$userid = "";
if($user->loggedIn()) {
    $userid = $_SESSION['userid'];
}
$data = [];

$videoData = null;

$likes = 0;
$dislikes = 0;
$hasLiked = false;

// Voting, only if user is logged in and corresponding post requests are made.
if($user->loggedIn()){
    if (isset($_POST['upvote'])){
        Video::videoVote($db, $videoid, $userid, true);
    }else if(isset($_POST['downvote'])){
        Video::videoVote($db, $videoid, $userid, false);
    }
}


//
// Load specific video, with corresponding data
//
Video::viewCountPlus($db, $videoid);

$videoData = Video::findVideo($db, $videoid);
$videoLikes = Video::findLikes($db, $videoid);

// Counts up likes and dislikes from db
if(isset($videoLikes)){
    foreach ($videoLikes as $like){
        if ($like['vote']==true){
            $likes++;
        }else if($like['vote']==false){
            $dislikes++;
        }
        if(isset($userid)) {
            if ($like['userid'] == $userid) {
                $hasLiked = true;
            }
        }
    }
}


//
// Load video comments
//

$comments = Comment::get($db, $videoid);

if ($user->loggedIn()){
    echo $twig->render('video.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'yes',
        'user' => $user->userData,
        'videoData' => $videoData,
        'likes' => $likes,
        'dislikes' => $dislikes,
        'hasLiked' => $hasLiked,
        'comments' => $comments,
    ));
} else {
    echo $twig->render('video.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
        'videoData' => $videoData,
        'likes' => $likes,
        'dislikes' => $dislikes,
        'comments' => $comments
    ));
}