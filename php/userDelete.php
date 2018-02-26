<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();
$userid = User::getLoggedInUserid();

User::deleteUser($db);

header("Location: /php/userLogout.php");