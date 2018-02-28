<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser();
$db     = Urge::requireDatabase();


list($playlistid, $videoid, $videoRank, $swap) = Urge::requireParameterArray(
    'playlist-id','video-id','video-rank', 'swap');


if($swap == 'up' && $videoRank > 0){
    $otherVideoId = Playlist::getVideoIdByRankPlaylist($db, $videoRank-1, $playlistid);
    if($videoid == $otherVideoId){header("Location: /playlist?id=".$playlistid);}

    if(Playlist::swapVideoRank($db, $playlistid, $videoid, $otherVideoId, $videoRank, $videoRank-1)){
        header("Location: /playlist?id=".$playlistid);
    }else{
        Urge::gotoError(500, "something went to shit.");
    }
}elseif ($swap == 'down' && $videoRank < (Playlist::getPlaylistLength($db, $playlistid)-1)){
    $otherVideoId = Playlist::getVideoIdByRankPlaylist($db, $videoRank+1, $playlistid);
    if($videoid == $otherVideoId){header("Location: /playlist?id=".$playlistid);}
    
    if(Playlist::swapVideoRank($db, $playlistid, $videoid, $otherVideoId, $videoRank, $videoRank+1)){
        header("Location: /playlist?id=".$playlistid);
    }else{
        Urge::gotoError(500, "something went to shit.");
    }
}else{
    header("Location: /playlist?id=".$playlistid);
}
