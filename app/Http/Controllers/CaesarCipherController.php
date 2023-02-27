<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaesarCipherController extends Controller
{   
    public function cesar(Request $request)
    {
        // get the input data from the form
        $text = $request->input('text');
        $key = $request->input('key');
        $decoded = $request->has('decode');

        // encode or decode the text based on the form input
        if ($decoded) {
            $result = $this->decode($text, $key);
        } else {
            $result = $this->encode($text, $key);
        }

        // return the view with the encoded or decoded text
        return view('cesar', ['result' => $result, 'decoded' => $decoded]);
    }

    public function encode($text, $key)
    {
        $result = '';

        $textLength = strlen($text);

        for ($i = 0; $i < $textLength; $i++) {
            $char = $text[$i];

            if (ctype_alpha($char)) {
                $ascii = ord(ctype_upper($char) ? 'A' : 'a');
                $offset = ((ord($char) + $key) - $ascii) % 26;
                $result .= chr($ascii + $offset);
            } else {
                $result .= $char;
            }
        }

        return $result;
    }

    public function decode($text, $key)
    {
        $result = '';

        $textLength = strlen($text);

        for ($i = 0; $i < $textLength; $i++) {
            $char = $text[$i];

            if (ctype_alpha($char)) {
                $ascii = ord(ctype_upper($char) ? 'A' : 'a');
                $offset = ((ord($char) - $key) - $ascii + 26) % 26;
                $result .= chr($ascii + $offset);
            } else {
                $result .= $char;
            }
        }

        return $result;
    }
}
