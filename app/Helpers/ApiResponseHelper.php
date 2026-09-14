<?php

namespace App\Helpers;

class ApiResponseHelper
{
    /**
     * Success JSON Response
     *
     * @param string|null $message Response message
     * @param mixed|null $data Response data
     * @param int $code HTTP status code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($message = null, $data = null, $code = 200)
    {
        return response()->json([
            KeywordHelper::STATUS  => KeywordHelper::SUCCESS,
            KeywordHelper::MESSAGE => $message,
            KeywordHelper::DATA    => $data,
        ], $code);
    }

    /**
     * Error JSON Response
     *
     * @param string|null $message Error message
     * @param mixed|null $errors Validation or error data
     * @param int $code HTTP status code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = null, $errors = null, $code = 422)
    {
        return response()->json([
            KeywordHelper::STATUS  => KeywordHelper::ERROR,
            KeywordHelper::MESSAGE => $message,
            KeywordHelper::ERRORS  => $errors,
        ], $code);
    }
}
