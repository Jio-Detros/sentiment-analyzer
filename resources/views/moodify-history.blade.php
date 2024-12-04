<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moodify - Sentiment History</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #191414;
            color: white;
            line-height: 1.6;
        }

        header {
            background-color: #1DB954;
            color: white;
            padding: 15px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        header a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            font-size: 1.1rem;
        }

        header a:hover {
            color: #b3e394;
        }

        .container {
            margin: 20px auto;
            max-width: 1200px;
            background: #282828;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        h1 {
            text-align: center;
            color: #1DB954;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #ddd;
        }

        th, td {
            border: 1px solid #333;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #1c1c1c;
            color: #1DB954;
        }

        td {
            word-wrap: break-word;
            max-width: 200px;
        }

        tr:nth-child(even) {
            background-color: #242424;
        }

        tr:hover {
            background-color: #333;
        }

        .actions button {
            margin: 5px;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 20px;
            transition: background-color 0.3s, transform 0.2s;
            font-weight: 600;
        }

        .delete-btn {
            background-color: #ff4d4d;
            color: white;
        }

        .delete-btn:hover {
            background-color: #d60000;
            transform: scale(1.1);
        }

        .report-btn {
            background-color: #1DB954;
            color: white;
        }

        .report-btn:hover {
            background-color: #16a34a;
            transform: scale(1.1);
        }

        #reportModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1c1c1c;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-height: 80%;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        #modalBackdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
        }

        #downloadReport, #closeModal {
            margin: 10px 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
        }

        #downloadReport {
            background-color: #1DB954;
            color: white;
        }

        #closeModal {
            background-color: #ff4d4d;
            color: white;
        }

        #downloadReport:hover {
            background-color: #16a34a;
            transform: scale(1.1);
        }

        #closeModal:hover {
            background-color: #d60000;
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('moodify') }}">Home</a>
        <a href="{{ route('analyze') }}">Analyze</a>
    </header>

    <div class="container">
        <h1>Sentiment Analysis History</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Input</th>
                    <th>Analysis Result</th>
                    <th>Emotion Detected</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sentiments as $sentiment)
                    <tr id="row-{{ $sentiment->id }}">
                        <td>{{ $sentiment->id }}</td>
                        <td>{{ $sentiment->input_text }}</td>
                        <td>{{ $sentiment->analysis_result }}</td>
                        <td>{{ $sentiment->emotion_detected }}</td>
                        <td>{{ $sentiment->analysis_date }}</td>
                        <td class="actions">
                            <button class="delete-btn" data-id="{{ $sentiment->id }}">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>


    <!-- Modal for Report -->
    <div id="reportModal">
        <div id="reportContent"></div>
        <button id="downloadReport">Download PDF</button>
        <button id="closeModal">Close</button>
    </div>
    <div id="modalBackdrop"></div>

    <script>
        $(document).ready(function () {
            // Delete sentiment logic
            $('.delete-btn').click(function () {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: "{{ route('softDelete', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () {
                            // Optionally, you can update the UI to reflect the soft delete, 
                            // such as hiding the row or showing a "soft deleted" status.
                            $('#row-' + id).remove();
                            alert('Item deleted successfully.');
                        },
                        error: function () {
                            alert('Failed to delete item.');
                        }
                    });
                }
            });
            // Triggered when the 'Generate Report' button is clicked
            $('.report-btn').click(function () {
                const id = $(this).data('id');
                $('#reportModal, #modalBackdrop').show();

                // AJAX request to generate the report
                $.ajax({
                    url: "{{ route('generateReport', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function (response) {
                        $('#reportContent').html(response); // Display the preview content inside the modal
                    },
                    error: function () {
                        alert('Failed to generate report.');
                    }
                });
            });

            $('#closeModal, #modalBackdrop').click(function () {
                $('#reportModal, #modalBackdrop').hide();
            });
        });
    </script>
</body>
</html>
