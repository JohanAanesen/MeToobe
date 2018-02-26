<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

list($email, $password) = Urge::requireParameterArray('email', 'password');
$db = Urge::requireDatabase();

$userid = User::login($db, $email, $password);
$userRemember = (isset($_POST['rememberMe'])) ? true : false;
if (!$userid) {
  Urge::gotoError(400, "Incorrect login credentials OR no connection.");
}
else{
  if($userRemember) {
    $days = time() + (86400 * 30); // 86400 = 1 day. Will expire after 30 days
    setcookie('email', $email, $days);
  }
}

Urge::gotoHome();
