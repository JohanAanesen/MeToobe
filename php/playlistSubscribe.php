<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();

$userid = Urge::requireLoggedInUser();

$playlistID = Urge::requireParameter('playlist-id');
$subscribe = Urge::requireParameter('subscribe');

if($subscribe == 'yes'){
    if (Playlist::subscribePlaylist($db, $userid, $playlistID)){
        header("Location: /playlist?id=".$playlistID);
    }else{
        Urge::gotoError(500, "Something went wrong subscribing to playlist");
    }
}elseif ($subscribe == 'no'){
    if(Playlist::unsubscribePlaylist($db, $userid, $playlistID)){
        header("Location: /playlist?id=".$playlistID);
    }else{
        Urge::gotoError(500, "Something went wrong unsubscribing from playlist");
    }
}


echo 'naniiiii';