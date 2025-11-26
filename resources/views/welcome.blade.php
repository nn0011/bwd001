<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
<!--
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
-->

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .title.m-b-md {
                text-transform: uppercase;
                color: #14a99b;
                font-weight: bold;
                text-shadow: -3px 2px 1px rgba(150, 150, 150, 1);
            }


            .welcome_home .top-right.links a {
                padding: 10px;
                color: #14a99b;
                font-weight: bold;
                letter-spacing: 0.3rem;
            }
            .welcome_home hr {
                width: 100%;
                max-width: 300px;
                border: 1px solid #14a99b;
            }

            .welcome_home .top-right.links a:hover {
                color: #fff;
            }
        </style>
    </head>
    <body class="welcome_home">
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                    <a href="{{ url('/home') }}">Home</a>
                    <a href="{{ url('/logout') }}">Logout</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md fade-in">
                    <img src="<?php echo LOGO_SRC; ?>">
                    <br />
                    <?php echo WD_NAME; ?>
                </div>
                <hr>
            </div>
        </div>


    <?php  include_once('../resources/views/layouts/hwd1_html/foot01.php');  ?>
    </body>



</html>
