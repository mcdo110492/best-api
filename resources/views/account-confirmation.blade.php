<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Account Activation Confirmation</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

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
                margin-bottom: 50px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    <img src="{{ asset('storage/best-logo.png') }}" alt="">
                </div>

                <div class="links">

                    @if ($status == 200)
                    <h1>{{ $message }}</h1>
                    @elseif ($status == 404)
                    <h1>{{ $message }}</h1>
                    @elseif ($status == 403)
                    <h1>{{ $message }}</h1>
                    @elseif ($status == 402)
                    <h1>{{ $message }}</h1>
                    @elseif ($status == 401)
                    <h1>{{ $message }}</h1>
                    <p>Click to request another activation code. <a href="http://127.0.0.1:8000/activation/resend?id={{ $id }}">Click Here</a> </p>
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>
