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
    <a class="navbar-brand text-success content-header-logo" href="#">#MeTube</a>

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
<div class="jumbotron" id="jumbo">
    <div class="container">
        <video width="100%" height="100%" controls>
            <source src="../movies/test.MP4" type="video/mp4">
        </video>
    </div>
</div>

<div class="container">

    <div class="row">
        <div class="col-md-9 col-xs-12">
            <div class="col-12 ml-lg-0">
                <h1 class="display-4">Comments</h1>
            </div>


            <div class="comment">
                <p class="comment-name">Ola Normann</p>
                <p class="comment-text">This is the shittiest fucking video I've ever fucking seen, kill yourself!</p>
            </div>
            <div class="comment">
                <p class="comment-name">Ola Normann</p>
                <p class="comment-text">This is the shittiest fucking video I've ever fucking seen, kill yourself!</p>
            </div>
            <div class="comment">
                <p class="comment-name">Ola Normann</p>
                <p class="comment-text">This is the shittiest fucking video I've ever fucking seen, kill yourself!</p>
            </div>
            <div class="comment">
                <p class="comment-name">Ola Normann</p>
                <p class="comment-text">This is the shittiest fucking video I've ever fucking seen, kill yourself!</p>
            </div>
        </div>
        <div class="col-md-3 col-xs-12">
            <div class="col-12 ml-lg-0">
                <h2 class="other-title">Other videos</h2>
            </div>
            <!-- Example row of columns -->
            <div class="row my-md-1">
                <div class="col-md-12">
                    <h2 class="lead text-limit">1234567890123456789012345678901234567890</h2>
                    <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
                </div>
                <div class="col-md-12">
                    <h2 class="lead text-limit">Video 2</h2>
                    <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
                </div>
                <div class="col-md-12">
                    <h2 class="lead text-limit">Video 3</h2>
                    <img src="../thumbnail/test.jpg" class="img-thumbnail border-success">
                </div>
            </div>
        </div>
    </div>

    <hr>

    <footer>
        <p>© UrgeWWW 2018</p>
    </footer>
</div> <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>


</body>
</html>