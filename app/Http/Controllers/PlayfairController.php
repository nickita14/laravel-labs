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
            $result = $this->decode($text, $matrix);
        } else {
            $result = $this->encode($text, $matrix);
        }

        // return the view with the encoded or decoded text
        return view('playfair', ['result' => $result, 'decoded' => $decoded]);
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
        foreach ($matrix as $row => $columns) {
            $columns = str_split($columns);
            $matrix[$row] = $columns;
        }
        
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
            $pos1 = false;
            foreach ($matrix as $row => $columns) {
                foreach ($columns as $column => $value) {
                    if ($value == $pair[0]) {
                        $pos1 = [$row, $column];
                        break 2;
                    }
                }
            }
            $pos2 = false;
            foreach ($matrix as $row => $columns) {
                foreach ($columns as $column => $value) {
                    if ($value == $pair[1]) {
                        $pos2 = [$row, $column];
                        break 2;
                    }
                }
            }

            // Check if $pos1 and $pos2 are valid indexes in the $matrix array.
            if ($pos1 === false || $pos2 === false) {
                // Handle the error, e.g., by skipping the pair or replacing it with a different pair.
                continue;
            }

            // Calculate the x and y coordinates of the first character using modulo and integer division.
            $column1 = $pos1[1];
            $row1 = $pos1[0];
            // Calculate the x and y coordinates of the second character using modulo and integer division.
            $column2 = $pos2[1];
            $row2 = $pos2[0];
            
            // Check if the indexes $row1, $column1, $row2, and $column2 are valid in the $matrix array.
            if ($row1 < 0 || $row1 >= 5 || $column1 < 0 || $column1 >= 5 || $row2 < 0 || $row2 >= 5 || $column2 < 0 || $column2 >= 5) {
                // Handle the error, e.g., by skipping the pair or replacing it with a different pair.
                continue;
            }
            
            // If the two characters are in the same row, replace each character with the character to its right.
            if ($column1 == $column2) {
                $row1 = ($row1 + 1 < 5) ? $row1 + 1 : 0;
                $row2 = ($row2 + 1 < 5) ? $row2 + 1 : 0;
                // Append the resulting character to the $encoded string.
                $encoded .= $matrix[$row1][$column1] . $matrix[$row2][$column2];
            // If the two characters are in the same column, replace each character with the character below it.
            } else if ($row1 == $row2) {
                $column1 = ($column1 + 1 < 5) ? $column1 + 1 : 0;
                $column2 = ($column2 + 1 < 5) ? $column2 + 1 : 0;
                $encoded .= $matrix[$row1][$column1] . $matrix[$row2][$column2];
            } else {
                $encoded .= $matrix[$row1][$column2] . $matrix[$row2][$column1];
            }
        }

        return $encoded;
    }

    private function decode($text, $matrix)
    {
        // Convert the ciphertext to all uppercase and remove any non-alphabetic characters.
        $text = strtoupper(preg_replace('/[^A-Za-z]/', '', $text));
        // Get the length of the resulting uppercase ciphertext.
        $textLength = strlen($text);
        $decoded = '';

        // Loop through the ciphertext, two characters at a time, using $i as the index.
        for ($i = 0; $i < $textLength; $i += 2) {
            // Extract the two-character substring starting from index $i and store it in a variable named $pair.
            $pair = substr($text, $i, 2);
            
            // Check if $pair has two characters before trying to access them.
            if (strlen($pair) < 2) {
                // Handle the error, e.g., by skipping the pair or replacing it with a different pair.
                continue;
            }

            // Find the positions of the two characters in the Playfair matrix and store them in $pos1 and $pos2.
            $pos1 = false;
            foreach ($matrix as $row => $columns) {
                foreach ($columns as $column => $value) {
                    if ($value == $pair[0]) {
                        $pos1 = [$row, $column];
                        break 2;
                    }
                }
            }
            $pos2 = false;
            foreach ($matrix as $row => $columns) {
                foreach ($columns as $column => $value) {
                    if ($value == $pair[1]) {
                        $pos2 = [$row, $column];
                        break 2;
                    }
                }
            }

            // Check if $pos1 and $pos2 are valid indexes in the $matrix array.
            if ($pos1 === false || $pos2 === false) {
                // Handle the error, e.g., by skipping the pair or replacing it with a different pair.
                continue;
            }

            // Calculate the x and y coordinates of the first character using modulo and integer division.
            $column1 = $pos1[1];
            $row1 = $pos1[0];
            // Calculate the x and y coordinates of the second character using modulo and integer division.
            $column2 = $pos2[1];
            $row2 = $pos2[0];

            // Check if the indexes $row1, $column1, $row2, and $column2 are valid in the $matrix array.
            if ($row1 < 0 || $row1 >= 5 || $column1 < 0 || $column1 >= 5 || $row2 < 0 || $row2 >= 5 || $column2 < 0 || $column2 >= 5) {
                // Handle the error, e.g., by skipping the pair or replacing it with a different pair.
                continue;
            }

             // If the two characters are in the same row, replace each character with the character to its left.
            if ($row1 == $row2) {
                $column1 = ($column1 - 1 >= 0) ? $column1 - 1 : 4;
                $column2 = ($column2 - 1 >= 0) ? $column2 - 1 : 4;
                $decoded .= $matrix[$row1][$column1] . $matrix[$row2][$column2];
            } else if ($column1 == $column2) {
                $row1 = ($row1 - 1 >= 0) ? $row1 - 1 : 4;
                $row2 = ($row2 - 1 >= 0) ? $row2 - 1 : 4;
                $decoded .= $matrix[$row1][$column1] . $matrix[$row2][$column2];
            } else {
                $decoded .= $matrix[$row1][$column2] . $matrix[$row2][$column1];
            }
        }
        return $decoded;
    }
}
