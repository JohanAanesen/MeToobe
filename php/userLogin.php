<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

list($email, $password) = Urge::requireParameterArray('email', 'password');
$db = Urge::requireDatabase();

$userid = User::login($db, $email, $password);
$userRemember = isset($_POST['rememberMe']);

// if doesn't exists
if (!$userid) {
  Urge::gotoError(400, "Incorrect login credentials OR no connection.");
}
else{

  if($userRemember) {                       // if user check 'rememberMe'
    $days = time() + (86400 * 30);          // days = 30 days (expires after 30 days)
    setcookie('email', $email, $days, "/"); // set cookie
  }
  else {  Urge::resetCookies(); }
}

Urge::gotoHome();
