<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();
$userid = Urge::requireUserid();

$updateCount = 0;

if(isset($_POST['teacher'])){
    $updateCount = User::updateType($db, $_POST['teacher'], "teacher"); // "teacher", $db);

}else if(isset($_POST['student'])){
    $updateCount = User::updateType($db, $_POST['student'], "student");

}else if(isset($_POST['admin'])){
    $updateCount = User::updateType($db, $_POST['admin'], "admin");

} else {
    Urge::gotoError(400, "Invalid request");
}

if(!$updateCount)
    Urge::gotoError(500, "There was a server error while updating the usertype");

Urge::gotoHome();
