<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

list($videoid, $commentid) = Urge::requireParameterArray('videoid', 'commentid');
$userid = Urge::requireLoggedInUser(); 
$db = Urge::requireDatabase();

if (!Comment::delete($db, $commentid)) {
    Urge::gotoError(500, "Server error, comment not deleted.");
}

Urge::gotoVideo($videoid);
