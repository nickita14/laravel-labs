<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DESController extends Controller
{
    const ENCRYPT = 0;
    const DECRYPT = 1;
    const PI = [58, 50, 42, 34, 26, 18, 10, 2,
      60, 52, 44, 36, 28, 20, 12, 4,
      62, 54, 46, 38, 30, 22, 14, 6,
      64, 56, 48, 40, 32, 24, 16, 8,
      57, 49, 41, 33, 25, 17, 9, 1,
      59, 51, 43, 35, 27, 19, 11, 3,
      61, 53, 45, 37, 29, 21, 13, 5,
      63, 55, 47, 39, 31, 23, 15, 7];
    const CP_1 = [57, 49, 41, 33, 25, 17, 9,
        1, 58, 50, 42, 34, 26, 18,
        10, 2, 59, 51, 43, 35, 27,
        19, 11, 3, 60, 52, 44, 36,
        63, 55, 47, 39, 31, 23, 15,
        7, 62, 54, 46, 38, 30, 22,
        14, 6, 61, 53, 45, 37, 29,
        21, 13, 5, 28, 20, 12, 4];
    const CP_2 = [14, 17, 11, 24, 1, 5, 3, 28,
        15, 6, 21, 10, 23, 19, 12, 4,
        26, 8, 16, 7, 27, 20, 13, 2,
        41, 52, 31, 37, 47, 55, 30, 40,
        51, 45, 33, 48, 44, 49, 39, 56,
        34, 53, 46, 42, 50, 36, 29, 32];
    const E = [32, 1, 2, 3, 4, 5,
     4, 5, 6, 7, 8, 9,
     8, 9, 10, 11, 12, 13,
     12, 13, 14, 15, 16, 17,
     16, 17, 18, 19, 20, 21,
     20, 21, 22, 23, 24, 25,
     24, 25, 26, 27, 28, 29,
     28, 29, 30, 31, 32, 1];
    const S_BOX = [

    [[14, 4, 13, 1, 2, 15, 11, 8, 3, 10, 6, 12, 5, 9, 0, 7],
    [0, 15, 7, 4, 14, 2, 13, 1, 10, 6, 12, 11, 9, 5, 3, 8],
    [4, 1, 14, 8, 13, 6, 2, 11, 15, 12, 9, 7, 3, 10, 5, 0],
    [15, 12, 8, 2, 4, 9, 1, 7, 5, 11, 3, 14, 10, 0, 6, 13],
    ],

    [[15, 1, 8, 14, 6, 11, 3, 4, 9, 7, 2, 13, 12, 0, 5, 10],
    [3, 13, 4, 7, 15, 2, 8, 14, 12, 0, 1, 10, 6, 9, 11, 5],
    [0, 14, 7, 11, 10, 4, 13, 1, 5, 8, 12, 6, 9, 3, 2, 15],
    [13, 8, 10, 1, 3, 15, 4, 2, 11, 6, 7, 12, 0, 5, 14, 9],
    ],

    [[10, 0, 9, 14, 6, 3, 15, 5, 1, 13, 12, 7, 11, 4, 2, 8],
    [13, 7, 0, 9, 3, 4, 6, 10, 2, 8, 5, 14, 12, 11, 15, 1],
    [13, 6, 4, 9, 8, 15, 3, 0, 11, 1, 2, 12, 5, 10, 14, 7],
    [1, 10, 13, 0, 6, 9, 8, 7, 4, 15, 14, 3, 11, 5, 2, 12],
    ],

    [[7, 13, 14, 3, 0, 6, 9, 10, 1, 2, 8, 5, 11, 12, 4, 15],
    [13, 8, 11, 5, 6, 15, 0, 3, 4, 7, 2, 12, 1, 10, 14, 9],
    [10, 6, 9, 0, 12, 11, 7, 13, 15, 1, 3, 14, 5, 2, 8, 4],
    [3, 15, 0, 6, 10, 1, 13, 8, 9, 4, 5, 11, 12, 7, 2, 14],
    ],

    [[2, 12, 4, 1, 7, 10, 11, 6, 8, 5, 3, 15, 13, 0, 14, 9],
    [14, 11, 2, 12, 4, 7, 13, 1, 5, 0, 15, 10, 3, 9, 8, 6],
    [4, 2, 1, 11, 10, 13, 7, 8, 15, 9, 12, 5, 6, 3, 0, 14],
    [11, 8, 12, 7, 1, 14, 2, 13, 6, 15, 0, 9, 10, 4, 5, 3],
    ],

    [[12, 1, 10, 15, 9, 2, 6, 8, 0, 13, 3, 4, 14, 7, 5, 11],
    [10, 15, 4, 2, 7, 12, 9, 5, 6, 1, 13, 14, 0, 11, 3, 8],
    [9, 14, 15, 5, 2, 8, 12, 3, 7, 0, 4, 10, 1, 13, 11, 6],
    [4, 3, 2, 12, 9, 5, 15, 10, 11, 14, 1, 7, 6, 0, 8, 13],
    ],

    [[4, 11, 2, 14, 15, 0, 8, 13, 3, 12, 9, 7, 5, 10, 6, 1],
    [13, 0, 11, 7, 4, 9, 1, 10, 14, 3, 5, 12, 2, 15, 8, 6],
    [1, 4, 11, 13, 12, 3, 7, 14, 10, 15, 6, 8, 0, 5, 9, 2],
    [6, 11, 13, 8, 1, 4, 10, 7, 9, 5, 0, 15, 14, 2, 3, 12],
    ],

    [[13, 2, 8, 4, 6, 15, 11, 1, 10, 9, 3, 14, 5, 0, 12, 7],
    [1, 15, 13, 8, 10, 3, 7, 4, 12, 5, 6, 11, 0, 14, 9, 2],
    [7, 11, 4, 1, 9, 12, 14, 2, 0, 6, 10, 13, 15, 3, 5, 8],
    [2, 1, 14, 7, 4, 10, 8, 13, 15, 12, 9, 0, 3, 5, 6, 11],
    ]
    ];
    const P = [16, 7, 20, 21, 29, 12, 28, 17,
     1, 15, 23, 26, 5, 18, 31, 10,
     2, 8, 24, 14, 32, 27, 3, 9,
     19, 13, 30, 6, 22, 11, 4, 25];
    const PI_1 = [40, 8, 48, 16, 56, 24, 64, 32,
        39, 7, 47, 15, 55, 23, 63, 31,
        38, 6, 46, 14, 54, 22, 62, 30,
        37, 5, 45, 13, 53, 21, 61, 29,
        36, 4, 44, 12, 52, 20, 60, 28,
        35, 3, 43, 11, 51, 19, 59, 27,
        34, 2, 42, 10, 50, 18, 58, 26,
        33, 1, 41, 9, 49, 17, 57, 25];
    const SHIFT = [1,1,2,2,2,2,2,2,1,2,2,2,2,2,2,1];
    private $password;
    private string $text = '';

    public function des(Request $request)
    {
        $text = $request->input('message');
        $key = $request->input('key');
        $decoded = $request->has('decode');

        // Encode or decode the text based on the form input
        if ($decoded) {
            $result = $this->decrypt($key, $text);
            $result = $this->handleResult($text, $key, $decoded);
        } else {
            $result = $this->encrypt($key, $text);
            $result = $this->handleResult($text, $key, $decoded);
        }

        // Return the view with the encoded or decoded text
        return view('des', ['result' => $result, 'decoded' => $decoded]);
    }

    public function run($key, $text, $action=self::ENCRYPT, $padding=False)
    {
        if (strlen($key) < 8) {
           echo ("Key Should be 8 bytes long");
        } elseif (strlen($key) > 8) {
            $key = substr($key, 0, 8);
        }

        $this->password = $key;

        if ($padding && $action == self::ENCRYPT) {
            $this->addPadding();
        } elseif (strlen($text) % 8 != 0) {
            echo ("Data size should be multiple of 8");
        }

        $keys = $this->generateKeys();
        $text_blocks = $this->nsplit($text, 8);
        $result = array();
        foreach ($text_blocks as $block) {
            $block = $this->stringToBitArray($block);
            $block = $this->permut($block, self::PI);
            list($g, $d) = $this->nsplit($block, 32);
            $tmp = null;
            for ($i = 0; $i < 16; $i++) {
                $d_e = $this->expand($d, self::E);
                if ($action == self::ENCRYPT) {
                    $tmp = $this->xor($keys[$i], $d_e);
                } else {
                    $tmp = $this->xor($keys[15 - $i], $d_e);
                }
                $tmp = $this->substitute($tmp);
                $tmp = $this->permut($tmp, self::P);
                $tmp = $this->xor($g, $tmp);
                $g = $d;
                $d = $tmp;
            }
            $result += $this->permut(array_merge($d, $g), self::PI_1);
        }
        $final_res = $this->bitArrayToString($result);
        if ($padding && $action == self::DECRYPT) {
            return $this->removePadding($final_res);
        } else {
            return $final_res;
        }
    }

    private function substitute($d_e)
    {
        $subblocks = $this->nsplit($d_e, 6);
        $result = array();
        for ($i = 0; $i < count($subblocks); $i++) {
            $block = str_split(implode("", $subblocks[$i]));
    
            // Add this check to ensure the array has enough elements
            if (count($block) < 6) {
                continue;
            }
            $row = bindec(strval($block[0]) . strval($block[5]));
    
            // Create an array from the sliced elements of the $block array
            $sliced_block = array_slice($block, 1, 4);
    
            // Pass the $sliced_block array to the implode() function
            $column = bindec(substr(implode("", $sliced_block), 0, 4));
            $val = self::S_BOX[$i][$row][$column];
            $bin = $this->binvalue($val, 4);
            $result = array_merge($result, str_split($bin));
        }
        return $result;
    }

    private function permut($block, $table)
    {
        // Get the largest value in the $table array
        $maxIndex = max($table);

        // If the size of the $block array is smaller than the largest value in the $table array
        if (!is_array($block) || count($block) < $maxIndex) {
            // Pad the $block array with zeros
            $block = array_pad((array) $block, $maxIndex, 0);
        }

        // Loop through the $table array
        $result = [];
        foreach ($table as $x) {
            $result[] = $block[$x - 1];
        }

        return $result;
    }

    public function expand($block, $table) {
        return array_map(function($x) use ($block) {
            return $block[$x-1];
        }, $table);
    }
    
    public function xor($t1, $t2) {
        return array_map(function($x, $y) {
            if (is_int($x) && is_int($y)) {
                return $x ^ $y;
            } elseif (is_string($x) && is_string($y)) {
                return (string)$x ^ (string)$y;
            } 
        }, $t1, $t2);
    }
    
    public function generateKeys() {
        $keys = [];
        // Convert the password to a bit array
        $keyBits = $this->stringToBitArray($this->password);
    
        // Apply the initial permutation
        $keyBits = $this->permut($keyBits, self::CP_1);
    
        // Split the key into two halves of 28 bits each
        list($leftHalf, $rightHalf) = $this->nsplit($keyBits, 28);
    
        $g = $leftHalf;
        $d = $rightHalf;
    
        // Generate 16 subkeys
        for ($i = 0; $i < 16; $i++) {
            // Apply the appropriate circular left shift to each half
            $g = $this->circularShiftLeft($g, self::SHIFT[$i]);
            $d = $this->circularShiftLeft($d, self::SHIFT[$i]);

            // Merge the halves back together and apply the permutation
            $subkey = implode("", $g) . implode("", $d);
            $subkey = $this->permut($subkey, self::CP_2);
    
            // Add the subkey to the list of keys
            array_push($keys, $subkey);
        }
        return $keys;
    }
    
    public function shift($g, $d, $n) {
        return [array_merge(array_slice($g, $n), array_slice($g, 0, $n)), 
               array_merge(array_slice($d, $n), array_slice($d, 0, $n))];
    }
    
    public function addPadding() {
        $pad_len = 8 - (strlen($this->text) % 8);
        $this->text .= str_repeat(chr($pad_len), $pad_len);
    }
    
    public function removePadding($data) {
        $pad_len = ord($data[strlen($data)-1]);
        return substr($data, 0, -$pad_len);
    }
    
    public function encrypt($key, $text, $padding=false) {
        return $this->run($key, $text, self::ENCRYPT, $padding);
    }
    
    public function decrypt($key, $text, $padding=false) {
        return $this->run($key, $text, self::DECRYPT, $padding);
    }

    public function nsplit($s, $n) {
        $chunks = array();

        // Check if $s is an array or a string
        $is_array = is_array($s);
        $length = $is_array ? count($s) : strlen($s);

        for ($i = 0; $i < $length; $i += $n) {
            if ($is_array) {
                $chunks[] = array_slice($s, $i, $n);
            } else {
                $chunks[] = substr($s, $i, $n);
            }
        }

        return $chunks;
    }

    public function stringToBitArray($text) {
        $array = array();
        for ($i = 0; $i < strlen($text); $i++) {
            $binval = $this->binvalue(ord($text[$i]), 8);
            $array = array_merge($array, str_split($binval));
        }
        return $array;
    }

    public function bitArrayToString($array) {
        $str = "";
        $chunks = array_chunk($array, 8);
        foreach ($chunks as $chunk) {
            $byte = "";
            foreach ($chunk as $bit) {
                $byte .= $bit;
            }
            $str .= chr(bindec($byte));
        }
        return $str;
    }

    public function binvalue($val, $bitsize) {
        $binval = decbin($val);
        if (strlen($binval) > $bitsize) {
            while (strlen($binval) < $bitsize) {
                $binval = "0" . $binval;
            }
            return $binval;
        }
    }

    public function circularShiftLeft($array, $positions) {
        $positions %= count($array);
        return array_merge(array_slice($array, $positions), array_slice($array, 0, $positions));
    }

    public function handleResult($text, $key, $decode) {
        $iv = "12345678";
        $text = $decode ? base64_decode($text) : $text;

        if ($decode) {
            return openssl_decrypt($text, "DES-CBC", $key, 1, $iv);
        } else {
            return base64_encode(openssl_encrypt($text, "DES-CBC", $key, 1, $iv));
        }
    }
}
