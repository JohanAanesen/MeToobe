<?php

if (!isset($_FILES['fileToUpload'])) {
  echo $twig->render('upload.html', array());
  exit();
}

header('Location: /video');