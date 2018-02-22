<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

list($email, $passord) = Urge::requireParameterArray('email', 'password');
$db = Urge::requireDatabase();

$userid = User::login($db, $email, $password);
if (!$userid) {
  Urge::gotoError(400, "Incorrect login credentials OR no connection.");
}
Urge::gotoHome();
