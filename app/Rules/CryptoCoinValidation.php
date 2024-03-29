<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CryptoCoinValidation implements ValidationRule
{


    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // List of known cryptocurrency coins
        $cryptoCoins = [
            'BTC', 'ETH', 'XRP', 'ADA', 'DOGE', 'BNB', 'SOL', 'DOT', 'LUNA', 'LINK'
        ];
        // Check if the string is in the list of available crypto coins
        if (!in_array(strtoupper($value), $cryptoCoins)) {
            $fail('This Crypto Coin Cannot Be Traded For Now');
        }
    }

}
