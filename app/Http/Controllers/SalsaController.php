<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ParagonIE\Sodium\Core\XSalsa20;

class SalsaController extends Controller
{
    public function salsa(Request $request)
    {   
        // Get the input and key from the form data.
        $input = $request->input('input');
        $plainTextKey = $request->input('key');
        $nonce = $request->input('nonce');
        $decoded = $request->has('decode');

        // Convert the plain text key to a suitable key for Salsa20 by hashing it using SHA-256
        $key = hash('sha256', $plainTextKey, true);

        if ($decoded) {
            // Decode the base64-encoded encrypted data and nonce
            $ciphertext = base64_decode($input);
            $nonce = base64_decode($nonce);

            // Decrypt the ciphertext
            $decrypted = XSalsa20::xsalsa20_xor($ciphertext, $nonce, $key);
            
            // Return the view with the encoded text.
            return view('salsa', ['result' => $decrypted, 'decoded' => true]);
        } else {
            // Generate a random nonce
            $nonce = random_bytes(24);

            // Encrypt the plain text
            $ciphertext = XSalsa20::xsalsa20_xor($input, $nonce, $key);

            // Encode the encrypted data and nonce in base64 format for storage or transmission
            $base64Ciphertext = base64_encode($ciphertext);
            $base64Nonce = base64_encode($nonce);

            // Return the view with the decoded text.
            return view('salsa', ['result' => $base64Ciphertext, 'nonce' => $base64Nonce, 'decoded' => false]);
        }
    }
}
