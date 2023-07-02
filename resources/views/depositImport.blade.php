<!DOCTYPE html>
<html>

<head>
    <title>Import Deposits File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="file"] {
            display: none;
        }

        .file-input-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2196F3;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .file-input-label:hover {
            background-color: #0d8bf0;
        }

        .file-name {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }

        .import-button {
            display: block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .import-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Import Deposits Histories File</h1>
        @if (session()->has('msg-success'))
            <div class="alert alert-success" role="alert">
                {{ session('msg-success') }}
            </div>
        @elseif (session()->has('msg-error'))
            <div class="alert alert-danger" role="alert">
                {{ session('msg-success') }}
            </div>
        @endif
        <form action="{{ url('/deposits/import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="clientFile">Select a deposit file:</label>
            <label for="fileInput" class="file-input-label">Browse</label>
            <input name="excel_file" type="file" id="fileInput">
            <span class="file-name"></span>
            <button type="submit" class="import-button">Import</button>
        </form>
    </div>



</body>

</html>
