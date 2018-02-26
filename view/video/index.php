<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$videoid = Urge::requireParameter('id');
$db      = Urge::requireDatabase();
$twig    = Urge::requireTwig();
$userid  = User::getLoggedInUserid();
$likes = 0;
$dislikes = 0;
$hasLiked = false;


// View-counter
Video::viewCountPlus($db, $videoid);

// Video title, desc, likes fetched from db
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
            //if hasLiked is true, the rendered buttons will not post the correct codes
            if ($like['userid'] == $userid) {
                $hasLiked = true;
            }
        }
    }
}

$videoData = Video::get($db, $videoid);
$comments  = Comment::get($db, $videoid);

$videoOwner = User::get($db, $videoData['userid']);
$videoData['fullname'] = $videoOwner['fullname'];

if ($userid) {
    $user = User::get($db, $userid);
    $userPlaylists = Playlist::getUserPlaylist($db, $userid);
    echo $twig->render('video.html', array(
        'title' => 'home',
        'loggedin' => 'yes',
        'user' => $user,
        'userid' => $userid,
        'videoData' => $videoData,
        'likes' => $likes,
        'dislikes' => $dislikes,
        'hasLiked' => $hasLiked,
        'comments' => $comments,
        'userPlaylists' => $userPlaylists,
    ));
} else {
    echo $twig->render('video.html', array(
        'title' => 'home',
        'loggedin' => 'no',
        'videoData' => $videoData,
        'likes' => $likes,
        'dislikes' => $dislikes,
        'comments' => $comments
    ));
}