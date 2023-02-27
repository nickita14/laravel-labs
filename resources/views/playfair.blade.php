<!DOCTYPE html>
<html>
<head>
    <title>Playfair Encryption</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin-top: 10px;
            margin-bottom: 5px;
        }
        #text, #key {
            display: block;
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            min-height: 100px;
            resize: none;
        }
        input[type=number] {
            width: 50px;
            text-align: center;
        }
        input[type=submit] {
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            background-color: #3f51b5;
            color: #fff;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background-color: #1a237e;
        }
        .result {
            margin-top: 20px;
            text-align: center;
        }
        .result p {
            margin-bottom: 5px;
        }
        .copy-btn {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            background-color: #ccc;
            color: #fff;
            cursor: pointer;
        }
        .copy-btn:hover {
            background-color: #999;
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
    <h1>Playfair Encryption</h1>
    <form method="post" action="{{ route('playfair') }}">
        @csrf
        <label for="text">Enter Text:</label>
        <textarea id="text" name="text"></textarea>
        <label for="key">Enter Key:</label>
        <input type="text" id="key" name="key">
        <input type="submit" value="Encode">
        <input type="submit" name="decode" value="Decode">
    </form>

    @if(isset($result))
        <div class="result">
            <p>{{ $decoded ? 'Decoded Text:' : 'Encoded Text:' }}</p>
            <p>{{ $result }}</p>
            <button class="copy-btn" onclick="copyToClipboard('{{ $result }}')">Copy to Clipboard</button>
        </div>
    @endif
</body>
</html>
