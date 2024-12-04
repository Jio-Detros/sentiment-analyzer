<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moodify - Sentiment History</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
        }

        header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        header a:hover {
            text-decoration: underline;
        }

        .container {
            margin: 20px auto;
            max-width: 1200px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        td {
            word-wrap: break-word;
            max-width: 150px;
        }

        .actions button {
            margin: 5px;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .delete-btn {
            background-color: #ff4d4d;
            color: white;
        }

        .delete-btn:hover {
            background-color: #d60000;
        }

        .report-btn {
            background-color: #4CAF50;
            color: white;
        }

        .report-btn:hover {
            background-color: #389E40;
        }

        #reportModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border: 1px solid #ccc;
            padding: 20px;
            z-index: 1000;
            width: 80%;
            max-height: 80%;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        #modalBackdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        #downloadReport, #closeModal {
            margin: 10px 5px;
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        #downloadReport {
            background-color: green;
            color: white;
        }

        #closeModal {
            background-color: red;
            color: white;
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
                            <!--<button class="report-btn" data-id="{{ $sentiment->id }}">Generate Report</button>-->
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
