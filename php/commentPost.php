<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

list($videoid, $comment) = Urge::requireParameterArray('videoid', 'comment');
$userid = Urge::requireLoggedInUser(); 
$db = Urge::requireDatabase();


$commentid = Comment::add($db, $userid, $videoid, $comment);

if (!commentid)
    Urge::gotoError(500, "Server was not successfull in adding comment");

Urge::gotoVideo($videoid);

