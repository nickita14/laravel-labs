<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHA-256 Hash</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f0f0;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-top: 50px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        textarea, input[type="text"] {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button[type="submit"] {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }
        button[type="submit"]:last-child {
            margin-right: 0;
        }
        p {
            margin-top: 20px;
            font-size: 18px;
        }
        .result {
            text-align: center;
        }
        .copy-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: block;
            font-size: 16px;
            border-radius: 5px;
            margin: 10px auto 0;
        }
    </style>
    <script>
        function copyToClipboard(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('Copied to clipboard!');
        }
    </script>
</head>
<body>
    <h1>SHA-256 Hashing</h1>
    <form method="post" action="{{ route('sha256') }}">
        @csrf
        <label for="message">Message:</label>
        <input type="text" id="message" name="message" required>
        <button type="submit">Hash</button>
    </form>
    @if(isset($result))
    <p class="result">Hashed Text: <br> {{ $result }}
        <button class="copy-btn" onclick="copyToClipboard('{{ $result }}')">Copy to Clipboard</button>
    </p>
    @endif
</body>
</html>
