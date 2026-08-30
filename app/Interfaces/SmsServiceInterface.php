<?php

namespace App\Interfaces;

interface SmsServiceInterface
{
    /**
     * Send SMS to the specified phone number.
     *
     * @param string $phoneNumber
     * @param string $message
     * @return void
     */
    public function send(string $phoneNumber, string $message): void;
}
