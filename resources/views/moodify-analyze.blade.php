<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyze Sentiments</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #191414;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: #1DB954;
        }

        a {
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            background-color: #1DB954;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            margin: 0 10px;
            transition: background-color 0.3s, transform 0.2s;
        }

        a:hover {
            background-color: #16a34a;
            transform: translateY(-2px);
        }

        a:active {
            transform: translateY(0);
        }

        textarea {
            width: 80%;
            height: 150px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 1rem;
        }

        textarea::placeholder {
            color: #ccc;
        }

        button {
            font-weight: 600;
            font-size: 1rem;
            color: white;
            background-color: #1DB954;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #16a34a;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        #successMessage {
            display: none;
            padding: 10px;
            margin-bottom: 20px;
            color: #1DB954;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }

        #analysisResults, #highlightedTextSection {
            margin-top: 20px;
            background-color: #282828;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
            width: 80%;
        }

        #highlightedText {
            white-space: pre-wrap;
            background-color: #1c1c1c;
            padding: 10px;
            border-radius: 5px;
        }

        canvas {
            margin-top: 20px;
            background-color: #1c1c1c;
            padding: 20px;
            border-radius: 10px;
        }

        footer {
            margin-top: 20px;
            color: #1DB954;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('moodify') }}">Home</a>
        <a href="{{ route('history') }}">View History</a>
    </header>

    <h1>Analyze Sentiments</h1>

    <!-- Success Message -->
    <div id="successMessage"></div>

    <!-- Sentiment Analysis Form -->
    <form id="analyzeForm" action="{{ route('store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <textarea name="input_text" id="input_text" placeholder="Enter text to analyze"></textarea>
        <br>
        <button type="submit">Analyze</button>
    </form>

    <!-- Results Section -->
    <div id="analysisResults" style="display: none;">
        <h2>Analysis Results:</h2>
        <p><strong>Input Text:</strong> <span id="inputText"></span></p>
        <p><strong>Positive Words:</strong> <span id="positiveCount"></span></p>
        <p><strong>Negative Words:</strong> <span id="negativeCount"></span></p>
        <p><strong>Positive Words Detected:</strong> <span id="positiveMatches"></span></p>
        <p><strong>Negative Words Detected:</strong> <span id="negativeMatches"></span></p>
        <p><strong>Overall Sentiment:</strong> <span id="sentimentResult"></span></p>
        <p><strong>Emotion:</strong> <span id="sentimentEmotion"></span></p>
    </div>

    <!-- Highlighted Text Section -->
    <div id="highlightedTextSection" style="display: none;">
        <h2>Highlighted Text:</h2>
        <p id="highlightedText"></p>
    </div>

    <!-- Add a Canvas for the Pie Chart -->
    <canvas id="sentimentPieChart" style="max-width: 400px;"></canvas>

    <footer>Moodify 2024. All rights reserved</footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        
        let sentimentChart = null; // Declare the chart variable globally

        $(document).on('submit', '#analyzeForm', function (e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('input_text', $('#input_text').val());
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("store") }}', // Ensure this matches your Laravel route name
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#inputText').text(response.input_text);
                    $('#positiveCount').text(response.positive_count);
                    $('#negativeCount').text(response.negative_count);
                    $('#positiveMatches').text(response.positive_matches.join(', '));
                    $('#negativeMatches').text(response.negative_matches.join(', '));
                    $('#sentimentResult').text(response.analysis_result);
                    $('#sentimentEmotion').text(response.emotion_detected);
                    $('#textFeatures').text(response.text_features);

                    let text = response.input_text;
                    let positiveWords = response.positive_matches;
                    let negativeWords = response.negative_matches;

                    let highlightedText = text.split(/\b/).map(word => {
                        let cleanWord = word.trim().toLowerCase();
                        if (positiveWords.includes(cleanWord)) {
                            return `<span style="background-color: #d4edda; color: #155724;">${word}</span>`;
                        } else if (negativeWords.includes(cleanWord)) {
                            return `<span style="background-color: #f8d7da; color: #721c24;">${word}</span>`;
                        }
                        return word;
                    }).join(' ');

                    $('#highlightedText').html(highlightedText);
                    $('#highlightedTextSection').show();
                    $('#analysisResults').show();
                    $('#successMessage')
                        .text('Input has been successfully analyzed.')
                        .fadeIn()
                        .delay(800)
                        .fadeOut();

                    const total = response.positive_count + response.negative_count + 1;
                    const neutralCount = Math.max(0, total - response.positive_count - response.negative_count);

                    const ctx = document.getElementById('sentimentPieChart').getContext('2d');

                    if (sentimentChart) {
                        sentimentChart.destroy();
                    }

                    sentimentChart = new Chart(ctx, {
                        type: 'bar', // Change this line to use a bar chart instead of a pie chart
                        data: {
                            labels: ['Positive', 'Negative', 'Neutral'],
                            datasets: [{
                                label: 'Sentiment Analysis',
                                data: [response.positive_count, response.negative_count, neutralCount],
                                backgroundColor: ['#28a745', '#dc3545', '#6c757d'],
                                borderColor: ['#155724', '#721c24', '#343a40'],
                                borderWidth: 1,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (tooltipItem) {
                                            const value = tooltipItem.raw;
                                            const percentage = ((value / total) * 100).toFixed(2);
                                            return `${tooltipItem.label}: ${value} (${percentage}%)`;
                                        },
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                },
                                y: {
                                    beginAtZero: true,
                                },
                            },
                        },
                    });
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        alert(xhr.responseJSON.error);
                    } else {
                        alert('An error occurred while analyzing the text.');
                    }
                }
            });
        });
    </script>
</body>
</html>