<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireUserid(); 
$db = Urge::requireDatabase();

if (!isset($_GET['videoid'])) {
    Urge::gotoError(400, "Bad request, no commentid");
}

if (!isset($_POST['commentid'])) {
    Urge::gotoError(400, "Bad request, no videoid");
}

$videoid = $_GET['videoid'];
$commentid = $_POST['commentid'];

if (!Comment::delete($db, $commentid)) {
    Urge::gotoError(500, "Server error, comment not deleted.");
}

Urge::gotoVideo($videoid);
