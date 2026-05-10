<?php

namespace App\Helpers;

class ApiResponseHelper
{
    public static function success($message = null, $data = null, $code = 200)
    {
        return response()->json([
            KeywordHelper::STATUS  => KeywordHelper::SUCCESS,
            KeywordHelper::MESSAGE => $message,
            KeywordHelper::DATA    => $data,
        ], $code);
    }

    public static function error($message = null, $errors = null, $code = 422)
    {
        return response()->json([
            KeywordHelper::STATUS  => KeywordHelper::ERROR,
            KeywordHelper::MESSAGE => $message,
            KeywordHelper::ERRORS  => $errors,
        ], $code);
    }
}
