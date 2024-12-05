<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moodify - Sentiment Analyzer</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #191414; /* Dark background */
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 2rem;
            color: #1DB954; /* Spotify Green */
        }

        a {
            text-decoration: none;
            font-weight: 600;
            font-size: 1.2rem;
            color: white;
            background-color: #1DB954;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            margin: 0 10px;
            transition: background-color 0.3s, transform 0.2s;
        }

        a:hover {
            background-color: #16a34a; /* Slightly darker green on hover */
            transform: translateY(-2px);
        }

        a:active {
            transform: translateY(0);
        }

        footer {
            position: absolute;
            bottom: 20px;
            color: #1DB954;
            font-size: 0.9rem;
        }
    </style>
</head>
    <h1>Welcome to Moodify</h1>
    <a href="{{ route('analyze') }}">Analyze Sentiments</a></br>
    <a href="{{ route('history') }}">View History</a>

    <footer>Moodify 2024. All rights reserved</footer>
</body>
</html>
