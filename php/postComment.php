<?php
session_start();
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/php/requirelogin.php";

require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/Comment.php";

$db = DB::getDBConnection();
$userid = $_SESSION['userid'];
$videoid = $_GET['videoid'];
$comment = $_POST['comment'];
$commentid = Comment::add($db, $userid, $videoid, $comment);

header("Location: /video?id=$videoid");
