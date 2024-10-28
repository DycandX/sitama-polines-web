<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8d7da;
            color: #721c24;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 20px;
            border: 1px solid #f5c6cb;
            background-color: #f8d7da;
            border-radius: 5px;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 0.5em;
        }
        p {
            margin-bottom: 1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500 | Server Error</h1>
        <p>Something went wrong on our end. Please try again later.</p>
        <a href="{{ url('/') }}">Go to Homepage</a>
    </div>
</body>
</html>
