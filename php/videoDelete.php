<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser();
$db     = Urge::requireDatabase();

if (!isset($_GET['videoid'])) {
    Urge::gotoError(400, "Bad request, missing videoid");
}

$videoid = $_GET['videoid'];

$checkOwner = Video::get($db, $videoid);

if($checkOwner['userid'] == $userid){
    Video::delete($db, $videoid);
}

Urge::gotoHome();