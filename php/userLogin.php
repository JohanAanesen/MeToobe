<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";
$db = Urge::requireDatabase();

print_r($_POST);

$email = $_POST['email'];
$name = $_POST['password'];

$userid = User::login($db, $email, $name);
if (!$userid) {
  Urge::gotoError(400, "Incorrect login credentials OR no connection.");
}
Urge::gotoHome();
