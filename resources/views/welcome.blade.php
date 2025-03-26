<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CBT Login</title>
    <style>
        body {
            background: url('/images/888.webp') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            background: rgba(19, 19, 19, 0.81);
            padding: 20px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 10px rgba(18, 239, 25, 0.97);
        }
        h1 {
            font-size: 48px;
            color: white;
            text-shadow: 2px 2px 5px rgba(244, 11, 11, 0.94);
            margin-bottom: 20px;
            
        }
        h2 {
            font-size: 30px;
            color: white;
            text-shadow: 2px 2px 10px rgba(244, 11, 11, 0.94);
            margin-bottom: 30px;
            
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            font-size: 18px;
            color: white;
            background: #ff5722;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn:hover {
            background:rgb(63, 24, 191);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>COMPUTER BASED TEST</h1>
        <h2>(CBT)</h2>
        <a href="{{ route('login') }}" class="btn">Login</a>
        <a href="{{ route('register') }}" class="btn">Register</a>
    </div>
</body>
</html>
