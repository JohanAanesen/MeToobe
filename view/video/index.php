<?php
session_start();

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/user.php";
require_once "$ROOT/classes/Video.php";

$db = DB::getDBConnection();

$user = new User($db);

$data = [];

$video = new Video($db);

$videoData = null;

$likes = 0;
$dislikes = 0;
$hasLiked = false;

if($user->loggedIn()){
    if (isset($_POST['upvote']) && isset($_POST['id'])){
        Video::videoVote($db, $_GET['id'], $_SESSION['userid'], true);
    }else if(isset($_POST['downvote']) && isset($_POST['id'])){
        Video::videoVote($db, $_GET['id'], $_SESSION['userid'], false);
    }
}

//if videoid is not set, return user to frontpage.
if (isset($_GET['id'])){
    $video->viewCountPlus($_GET['id']);

    $videoData = $video->findVideo($_GET['id']);

    $videoLikes = Video::findLikes($db, $_GET['id']);
    if(isset($videoLikes)){
        foreach ($videoLikes as $like){
            if ($like['vote']==true){
                $likes++;
            }else if($like['vote']==false){
                $dislikes++;
            }

            if($like['userid']==$_SESSION['userid']){
                $hasLiked = true;
            }

        }
    }
}else{
    header("Location: /");
}



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
    ));
}else{
    echo $twig->render('video.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
        'videoData' => $videoData,
        'likes' => $likes,
        'dislikes' => $dislikes,
    ));
}