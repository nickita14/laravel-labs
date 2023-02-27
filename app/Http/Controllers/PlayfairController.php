<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayfairController extends Controller
{
    public function playfair(Request $request)
    {
        // get the input data from the form
        $text = $request->input('text');
        $key = $request->input('key');
        $decoded = $request->has('decode');

        // generate the Playfair matrix from the key
        $matrix = $this->generateMatrix($key);

        // encode or decode the text based on the form input
        if ($decoded) {
            $encoded = $this->decode($text, $matrix);
        } else {
            $encoded = $this->encode($text, $matrix);
        }

        // return the view with the encoded or decoded text
        return view('playfair', ['encoded' => $encoded, 'decoded' => $decoded]);
    }

    private function generateMatrix($key)
    {   
        $key = strtoupper($key);
        $key = array_unique(str_split($key));
        $key = implode($key);
        $keyLength = strlen($key);
        $table = '';

        // fill the table with the unique characters in the key
        for ($i = 0; $i < $keyLength; $i++) {
            if (strpos($table, $key[$i]) === false) {
                $table .= $key[$i];
            }
        }

        // fill the table with the remaining characters of the alphabet
        for ($i = 65; $i <= 90; $i++) {
            $letter = chr($i);
            if ($letter == 'J') {
                continue;
            }
            if (strpos($table, $letter) === false) {
                $table .= $letter;
            }
        }

        // split the table into a 5x5 matrix
        $matrix = str_split($table, 5);

        return $matrix;
    }

    private function encode($text, $matrix)
    {
        // Convert the plaintext to all uppercase and remove any non-alphabetic characters.
        $text = strtoupper(preg_replace('/[^A-Za-z]/', '', $text));
        // Get the length of the resulting uppercase plaintext.
        $textLength = strlen($text);
        $encoded = '';

        // Loop through the plaintext, two characters at a time, using $i as the index.
        for ($i = 0; $i < $textLength; $i += 2) {
            // Extract the two-character substring starting from index $i and store it in a variable named $pair.
            $pair = substr($text, $i, 2);
            
            // Check if $pair has two characters before trying to access them.
            if (strlen($pair) < 2) {
                $pair .= 'X'; // Pad with 'X' if $pair has only one character
            }

            // Find the positions of the two characters in the Playfair matrix and store them in $pos1 and $pos2.
            $pos1 = array_search($pair[0], $matrix);
            $pos2 = array_search($pair[1], $matrix);

            // Check if $pos1 and $pos2 are valid indexes in the $matrix array.
            if ($pos1 === false || $pos2 === false) {
                // Handle the error, e.g., by skipping the pair or replacing it with a different pair.
                continue;
            }

            // Calculate the x and y coordinates of the first character using modulo and integer division.
            $x1 = $pos1 % 5;
            $y1 = intval($pos1 / 5);
            // Calculate the x and y coordinates of the second character using modulo and integer division.
            $x2 = $pos2 % 5;
            $y2 = intval($pos2 / 5);
            
            // Check if the indexes $y1, $x1, $y2, and $x2 are valid in the $matrix array.
            if ($y1 < 0 || $y1 >= 5 || $x1 < 0 || $x1 >= 5 || $y2 < 0 || $y2 >= 5 || $x2 < 0 || $x2 >= 5) {
                // Handle the error, e.g., by skipping the pair or replacing it with a different pair.
                continue;
            }

            // If the two characters are in the same row, replace each character with the character to its right.
            if ($x1 == $x2) {
                $y1 = ($y1 + 1) % 5;
                $y2 = ($y2 + 1) % 5;
                // Append the resulting character to the $encoded string.
                $encoded .= $matrix[$y1 * 5 + $x1] . $matrix[$y2 * 5 + $x2];
            // If the two characters are in the same column, replace each character with the character below it.
            } else if ($y1 == $y2) {
                $x1 = ($x1 + 1) % 5;
                $x2 = ($x2 + 1) % 5;
                $encoded .= $matrix[$y1 * 5 + $x1] . $matrix[$y2 * 5 + $x2];
            } else {
                $encoded .= $matrix[$y1 * 5 + $x2] . $matrix[$y2 * 5 + $x1];
            }
        }

        return $encoded;
    }

    private function decode($text, $matrix)
    {
        // replace J with I in the input text
        $text = str_replace('J', 'I', $text);

        // split the input text into digraphs
        $digraphs = str_split($text, 2);

        $decoded = '';
        foreach ($digraphs as $digraph) {
            $char1 = $digraph[0];
            $char2 = $digraph[1];

            $pos1 = $this->getPosition($char1, $matrix);
            $pos2 = $this->getPosition($char2, $matrix);

            if (isset($pos1) && isset($pos2) && $pos1['row'] == $pos2['row']) {
                // same row
                $decoded .= $matrix[$pos1['row']][($pos1['col'] - 1 + 5) % 5];
                $decoded .= $matrix[$pos2['row']][($pos2['col'] - 1 + 5) % 5];
            } elseif ($pos1['col'] == $pos2['col']) {
                // same column
                $decoded .= $matrix[($pos1['row'] - 1 + 5) % 5][$pos1['col']];
                $decoded .= $matrix[($pos2['row'] - 1 + 5) % 5][$pos2['col']];
            } else {
                // different row and column
                $decoded .= $matrix[$pos1['row']][$pos2['col']];
                $decoded .= $matrix[$pos2['row']][$pos1['col']];
            }
        }

        return $decoded;
    }

    private function getPosition($table, $letter)
    {
        // Iterate over each row in the table
        for ($i = 0; $i < 5; $i++) {
            // Iterate over each column in the row
            for ($j = 0; $j < 5; $j++) {
                // If the current letter in the table matches the letter we're looking for,
                // return the row and column index
                if (!empty($table) && isset($table[$i]) && strlen($table[$i]) > $j && $table[$i][$j] == $letter) {
                    return array($i, $j);
                }
            }
        }
    }
}
