<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();
$userid = User::getLoggedInUserid();

User::delete($db, $userid);

header("Location: /php/userLogout.php");