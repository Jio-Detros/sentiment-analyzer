<!DOCTYPE html>
<html>
<head>
    <title>Sentiment Analysis Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 20px auto;
            width: 80%; /* Adjust the width as needed */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            margin-bottom: 15px;
        }
        p {
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sentiment Analysis Report</h1>
    </div>
    <div class="content">
        <p><strong>Input Text:</strong> {{ $input }}</p>
        <p><strong>Sentiment Result:</strong> {{ $result }}</p>
        <p><strong>Emotion:</strong> {{ $emotion }}</p>
        <p><strong>Text Features:</strong> {{ $text_features }}</p>
        <p><strong>Date:</strong> {{ $date }}</p>
    </div>
    <div class="footer">
        <p>Generated by Moodify Sentiment Analysis Tool</p>
    </div>
</body>
</html>
