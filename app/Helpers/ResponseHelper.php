<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($message = null, $data = null)
    {
        return [
            KeywordHelper::STATUS  => KeywordHelper::SUCCESS,
            KeywordHelper::MESSAGE => $message,
            KeywordHelper::DATA    => $data,
        ];
    }

    public static function error($message = null, $errors = null)
    {
        return [
            KeywordHelper::STATUS  => KeywordHelper::ERROR,
            KeywordHelper::MESSAGE => $message,
            KeywordHelper::ERRORS  => $errors,
        ];
    }
}
