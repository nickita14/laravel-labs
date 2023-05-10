<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SHA256Controller extends Controller
{
    public function sha256(Request $request)
    {
        $message = $request->input('message');

        // Validate the user input
        $request->validate([
            'message' => 'required|string'
        ]);

        // Hash the user input using the Sha class
        $hash = self::shaHashing($message);

        // Convert the binary hash to a hexadecimal string
        $hashHex = bin_to_hex($hash, $message);

        // Return the hashed text as a response
        return view('sha256', ['result' => $hashHex]);
    }

    // Method for computing the SHA-256 hash value of a message
    public static function shaHashing($message) {
        $K = [
            0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
            0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
            0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
            0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
            0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
            0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
            0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
            0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
        ];

        // Calculate length of message in bits and append padding bits
        $length = strlen($message) * 8;
        $message .= chr(0x80);
        while ((strlen($message) * 8 + 64) % 512 != 0) {
            $message .= chr(0x00);
        }
    
        // Append length of message in bits
        $message .= pack("J", $length);
    
        // Check that padding completed properly
        assert((strlen($message) * 8) % 512 == 0, "Padding did not complete properly!");
    
        // Divide message into blocks of 512 bits (64 bytes)
        $blocks = str_split($message, 64);
    
        // Initialize hash values
        $h0 = 0x6a09e667;
        $h1 = 0xbb67ae85;
        $h2 = 0x3c6ef372;
        $h3 = 0xa54ff53a;
        $h4 = 0x510e527f;
        $h5 = 0x9b05688c;
        $h6 = 0x1f83d9ab;
        $h7 = 0x5be0cd19;    

        foreach ($blocks as $message_block) {
            $message_schedule = [];
            // Prepare message schedule for this block
            for ($t = 0; $t < 64; $t++) {
                if ($t <= 15) {
                    $message_schedule[] = substr($message_block, $t*4, 4);
                } else {
                    $term1 = self::_sigma1(bindec(bin2hex($message_schedule[$t-2])));
                    $term2 = bindec(bin2hex($message_schedule[$t-7]));
                    $term3 = self::_sigma0(bindec(bin2hex($message_schedule[$t-15])));
                    $term4 = bindec(bin2hex($message_schedule[$t-16]));
    
                    $schedule = ($term1 + $term2 + $term3 + $term4) % 2**32;
                    $message_schedule[] = pack("N", $schedule);
                }
            }
    
            assert(count($message_schedule) == 64);
    
            // Initialize hash values for this block
            $a = $h0;
            $b = $h1;
            $c = $h2;
            $d = $h3;
            $e = $h4;
            $f = $h5;
            $g = $h6;
            $h = $h7;
    
            // Main loop
            for ($t = 0; $t < 64; $t++) {
                // Calculate t1 and t2 using the SHA-256 algorithm
                $t1 = ($h + self::_capsigma1($e) + self::_ch($e, $f, $g) + $K[$t] + bindec(bin2hex($message_schedule[$t]))) % 2**32;
                $t2 = (self::_capsigma0($a) + self::_maj($a, $b, $c)) % 2**32;
                
                // Update the variables a through h using the SHA-256 algorithm
                $h = $g;
                $g = $f;
                $f = $e;
                $e = ($d + $t1) % 2**32;
                $d = $c;
                $c = $b;
                $b = $a;
                $a = ($t1 + $t2) % 2**32;
            }
            
            // Update the hash values using the SHA-256 algorithm
            $h0 = ($h0 + $a) % 2**32;
            $h1 = ($h1 + $b) % 2**32;
            $h2 = ($h2 + $c) % 2**32;
            $h3 = ($h3 + $d) % 2**32;
            $h4 = ($h4 + $e) % 2**32;
            $h5 = ($h5 + $f) % 2**32;
            $h6 = ($h6 + $g) % 2**32;
            $h7 = ($h7 + $h) % 2**32;
            
        }
        // Return the packed hash values as a binary string
        return pack("N", $h0) . pack("N", $h1) . pack("N", $h2) . pack("N", $h3) . pack("N", $h4) .pack("N", $h5) . pack("N", $h6) . pack("N", $h7);
    }

    // Private helper functions for the SHA-256 algorithm
    /**
    * Applies the sigma0 function on the given number.
    *
    * @param int $num
    * @return int
    */
    private static function _sigma0(int $num): int
    {
        $num = (self::_rotate_right($num, 7) ^ self::_rotate_right($num, 18) ^ ($num >> 3));
        return $num;
    }

    /**
     * Applies the sigma1 function on the given number.
     *
     * @param  int  $num
     * @return int
     */
    private static function _sigma1(int $num): int
    {
        $num = (self::_rotate_right($num, 17) ^ self::_rotate_right($num, 19) ^ ($num >> 10));
        return $num;
    }

    /**
     * Applies the capsigma0 function on the given number.
     *
     * @param  int  $num
     * @return int
     */
    private static function _capsigma0(int $num): int
    {
        $num = (self::_rotate_right($num, 2) ^ self::_rotate_right($num, 13) ^ self::_rotate_right($num, 22));
        return $num;
    }

    /**
     * Applies the capsigma0 function on the given number.
     *
     * @param  int  $num
     * @return int
     */
    private static function _capsigma1(int $num): int
    {
        $num = (self::_rotate_right($num, 6) ^ self::_rotate_right($num, 11) ^ self::_rotate_right($num, 25));
        return $num;
    }

    /**
     * Applies the ch function on the given numbers.
     * @param int $x
     * @param int $y
     * @param int $z
     * @return int
     */
    private static function _ch(int $x, int $y, int $z): int
    {
        return ($x & $y) ^ (~$x & $z);
    }

    /**
     * Applies the maj function on the given numbers.
     * @param int $x
     * @param int $y
     * @param int $z
     * @return int
     */
    private static function _maj(int $x, int $y, int $z): int
    {
        return ($x & $y) ^ ($x & $z) ^ ($y & $z);
    }

    /**
     * Performs a right circular shift on the given number by the specified shift value.
     * @param int $num
     * @param int $shift
     * @param int $size
     * @return int
     */
    private static function _rotate_right(int $num, int $shift, int $size = 32): int
    {
        return ($num >> $shift) | ($num << ($size - $shift));
    }
}

function bin_to_hex($hash, $message)
{
    return hash('sha256', $message);
}