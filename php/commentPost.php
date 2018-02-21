<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireUserid(); 
$db = Urge::requireDatabase();

if (!isset($_GET['videoid'])) {
    Urge::gotoError(400, "Bad request, no videoid");
}

if (!isset($_POST['comment'])) {
    Urge::gotoError(400, "Bad request, no comment attached to this request");
}

$videoid = $_GET['videoid'];
$comment = $_POST['comment'];

$commentid = Comment::add($db, $userid, $videoid, $comment);

if (!commentid)
    Urge::gotoError(500, "Server was not successfull in adding comment");

Urge::gotoVideo($videoid);

