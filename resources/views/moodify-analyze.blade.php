<!DOCTYPE html>
<html>
<head>
    <title>Analyze Sentiments</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #successMessage {
            display: none;
            padding: 10px;
            margin-bottom: 10px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }

        #highlightedTextSection {
            display: none;
        }
    </style>
</head>
<body>
    <a href="{{ route('moodify') }}">Home</a> |
    <a href="{{ route('history') }}">View History</a>
    <h1>Analyze Sentiments</h1>

    <!-- Success Message -->
    <div id="successMessage"></div>

    <!-- Sentiment Analysis Form -->
    <form id="analyzeForm" action="{{ route('store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <textarea name="input_text" id="input_text" placeholder="Enter text to analyze"></textarea><br>
        <button type="submit">Analyze</button>
    </form>


    <!-- Results Section -->
    <div id="analysisResults" style="display: none; margin-top: 20px;">
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
    <div id="highlightedTextSection" style="margin-top: 20px;">
        <h2>Highlighted Text:</h2>
        <p id="highlightedText" style="white-space: pre-wrap; background-color: #f9f9f9; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></p>
    </div>

    <!-- Add a Canvas for the Pie Chart -->
    <canvas id="sentimentPieChart" style="max-width: 400px; max-height: 400px; margin: auto; display: block;"></canvas>

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
                        type: 'pie',
                        data: {
                            labels: ['Positive', 'Negative', 'Neutral'],
                            datasets: [{
                                data: [response.positive_count, response.negative_count, neutralCount],
                                backgroundColor: ['#28a745', '#dc3545', '#6c757d'],
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
