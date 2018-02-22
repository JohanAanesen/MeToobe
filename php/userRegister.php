<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";
$db = Urge::requireDatabase();

list($newname, $newemail, $newpassword) = Urge::requireParameterArray('newname','newemail', 'newpassword');
$wannabeTeacher = (isset($_POST['isTeacher'])) ? true : false;

$userid = User::create($db, $newname, $newemail, $newpassword, $wannabeTeacher);
if (!$userid) {
  Urge::gotoError(400, "A user with given email already exists OR There might have been a database error.");
}
$loggedinUserid = User::login($db, $newemail, $newpassword);
if (!$loggedinUserid) {
  Urge::gotoError(500, "The server encountered an error while logging in.");
} 

Urge::gotoHome();
