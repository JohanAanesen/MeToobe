<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="MeTube the new and shittier youtube duplicate!">
    <meta name="author" content="Johan Aanesen">
    <link rel="icon" href="">

    <title>Sign in - MeTube</title>

    <?php
    include_once("head.htm");
    ?>

</head>

<body style="padding-top: 40px; padding-bottom: 40px;
    background: #3a6186; /* fallback for old browsers */
    background: -webkit-linear-gradient(to left, #1C1F4F , #134D36); /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to left, #1C1F4F , #134D36);">



<div class="text-center text-white">
    <h1 class="display-2"><span class="content-header wow fadeIn " data-wow-delay="0.2s" data-wow-duration="2s">#MeToobe</span></h1>
</div>




<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-12 form-line">
            <form class="form-signin form-group">
                <h2 class="form-signin-heading text-white">Sign in</h2>
                <label for="inputEmail" class="sr-only">Email address</label>
                <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required="" autofocus="">
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" class="form-control" placeholder="Password" required="">
                <div class="checkbox">
                    <!--    <label>
                            <input type="checkbox" value="remember-me"> Remember me
                        </label>-->
                    </div>
                <button class="btn btn-default bg-transparent btn-block border border-white text-white" type="submit">Sign in</button>
            </form>
        </div>

        <div class="col-md-6 col-sm-12">
            <form class="form-signin form-group">
                <h2 class="form-signin-heading text-white">Register</h2>
                <label for="inputEmail" class="sr-only">Email address</label>
                <input type="email" id="registerEmail" class="form-control" placeholder="Email address" required="" autofocus="">
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="registerPassword" class="form-control" placeholder="Password" required="">
                <div class="checkbox">
                    <label class="text-white">
                        <input type="checkbox" value="remember-me" > I am a teacher
                    </label>
                </div>
                <button class="btn btn-default bg-transparent btn-block border border-white text-white" type="submit">Register</button>
            </form>

        </div>
    </div>
</div>
 <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->


</body>
</html>