<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title>MeTube</title>

    <?php
        include_once("head.htm");
    ?>

</head>

<body> <!-- style="font-family: sans-serif; "> -->

<nav class="navbar navbar-inverse navbar-static-top">
    <a class="navbar-brand text-success content-header-logo" href="#">#MeToobe</a>

    <form class="mx-2 my-auto d-inline w-50">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="imt2631..">
            <span class="input-group-btn">
                <button class="btn btn-outline-success ml-lg-1" type="button">GO</button>
            </span>
        </div>
    </form>

    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link text-success" href="#">example@gmail.com</a>
        </li>
    </ul>
</nav>

<!-- Main jumbotron -->
<div class="jumbotron">
    <div class="container">
        <h1>Holy fucking shit you need to register mate!</h1>
        <p>Smash this button and get to it.</p>
        <p><a class="btn btn-success btn-lg" href="login.php" role="button">Register »</a></p>
    </div>
</div>

<div class="container">

    <div class="col-12 ml-lg-0">
        <h1 class="display-4">New Videos</h1>
    </div>

    <!-- Example row of columns -->
    <div class="row my-md-1">
        <div class="col-md-4">
            <h2 class="lead text-limit">1234567890123456789012345678901234567890</h2>
            <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
        </div>
        <div class="col-md-4">
            <h2 class="lead text-limit">Video 2</h2>
            <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
        </div>
        <div class="col-md-4">
            <h2 class="lead text-limit">Video 3</h2>
            <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
        </div>
        <div class="col-md-4">
            <h2 class="lead text-limit">Video 4</h2>
            <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
        </div>
        <div class="col-md-4">
            <h2 class="lead text-limit">Video 5</h2>
            <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
        </div>
        <div class="col-md-4">
            <h2 class="lead text-limit">Video 6</h2>
            <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
        </div>
    </div>

    <hr>

    <footer>
        <p>© UrgeWWW 2017</p>
    </footer>
</div> <!-- /container -->


<!-- Bootstrap JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="../vendor/twbs/bootstrap/dist/js/bootstrap.js"></script>


</body>
</html>