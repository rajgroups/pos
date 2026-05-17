<?php

namespace App\Helpers;

class GeneralHelper
{
    public static function OtpGenerator($digit = true, $length = 6)
    {
        if ($digit) {

            // Numbers only
            $characters = '0123456789';

        } else {

            // Alphabets + numbers
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        }

        $otp = '';

        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $otp;
    }
}
