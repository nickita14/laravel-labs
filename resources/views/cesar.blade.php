<!DOCTYPE html>
<html>
<head>
    <title>Cesar Encryption</title>
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
        textarea, input[type="number"] {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"] {
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
        input[type="submit"]:last-child {
            margin-right: 0;
        }
        p {
            margin-top: 20px;
            font-size: 18px;
        }
        .copy-btn {
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
        }
        .result {
            text-align: center;
        }
        .copy-btn {
            display: block;
            margin: 10px auto 0;
        }
    </style>
</head>
<body>
    <h1>Cesar Encryption</h1>
    <form method="post" action="{{ route('cesar') }}">
        @csrf
        <label for="text">Enter Text:</label>
        <textarea id="text" name="text"></textarea>
        <label for="key">Enter Key:</label>
        <input type="number" id="key" name="key" min="1" max="25" value="3">
        <input type="submit" value="Encode">
        <input type="submit" name="decode" value="Decode">
    </form>

    @if(isset($result))
    <p class="result">{{ $decoded ? 'Decoded Text:' : 'Encoded Text:' }} {{ $result }}
        <button class="copy-btn" onclick="copyToClipboard('{{ $result }}')">Copy to Clipboard</button>
    </p>
    @endif


    <!-- Add a hidden input field to store the copied text -->
    <input type="hidden" id="copy-input" value="">
    
    <!-- Add a JavaScript function to copy the text to clipboard -->
    <script>
        function copyToClipboard(text) {
            var input = document.createElement('textarea');
            input.innerHTML = text;
            document.body.appendChild(input);
            input.select();
            var result = document.execCommand('copy');
            document.body.removeChild(input);
            return result;
        }

        var copyToClipboardButton = document.querySelector('#copy-to-clipboard');
        if (copyToClipboardButton) {
            copyToClipboardButton.addEventListener('click', function() {
                var resultText = document.querySelector('p').textContent;
                copyToClipboard(resultText);
            });
        }
    </script>

</body>
</html>
