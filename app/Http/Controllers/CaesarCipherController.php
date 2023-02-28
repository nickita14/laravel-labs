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
        // Initialize an empty string to store the result.
        $result = ''; 
        // Get the length of the input text.
        $textLength = strlen($text); 

        // Iterate over each character in the input text.
        for ($i = 0; $i < $textLength; $i++) { 
            // Get the current character.
            $char = $text[$i]; 

            // If the character is an alphabetic letter.
            if (ctype_alpha($char)) { 
                // Get the ASCII value of the first letter in the alphabet.
                $ascii = ord(ctype_upper($char) ? 'A' : 'a'); 
                // Calculate the offset for the current character using the encryption key.
                $offset = ((ord($char) + $key) - $ascii) % 26; 
                // Append the encoded character to the result string.
                $result .= chr($ascii + $offset); 
                // If the character is not an alphabetic letter.
            } else { 
                // Append the original character to the result string.
                $result .= $char; 
            }
        }

        return $result; 
    }

    public function decode($text, $key)
    {
        // Initialize an empty string to store the result.
        $result = ''; 

        // Get the length of the input text.
        $textLength = strlen($text); 

        // Iterate over each character in the input text.
        for ($i = 0; $i < $textLength; $i++) { 
            // Get the current character.
            $char = $text[$i]; 
            // If the character is an alphabetic letter.
            if (ctype_alpha($char)) { 
                // Get the ASCII value of the first letter in the alphabet.
                $ascii = ord(ctype_upper($char) ? 'A' : 'a'); 
                // Calculate the offset for the current character using the encryption key.
                $offset = ((ord($char) - $key) - $ascii + 26) % 26; 
                // Append the decoded character to the result string.
                $result .= chr($ascii + $offset); 
                // If the character is not an alphabetic letter.
            } else { 
                // Append the original character to the result string.
                $result .= $char; 
            }
        }

        return $result; 
    }
}
