<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db     = Urge::requireDatabase();
$twig   = Urge::requireTwig();
$userid = User::getLoggedInUserid();

$user = null;
if ($userid) {
    $user = User::get($db, $userid);
    if (!isset($user))
        Urge::gotoError('Server encountered an error. It should be possible to get user information from logged in user.');
}


$newVideos = Video::getNewVideos($db);
$newVideos = Urge::encodeThumbnailsToBase64($newVideos);

echo $twig->render('home.html', array(
    'title' => 'home',
    'userid' => $userid,
    'user' => $user,
    'wannabeUsers' => User::getWannabeTeachers($db),
    'admin' => User::isAdmin(),
    'newVideos' => $newVideos,
));


