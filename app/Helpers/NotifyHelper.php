<?php
namespace App\Helpers;

class NotifyHelper
{
    public static function success(string $key)
    {
        notyf()->addSuccess(__('string.' . $key));
    }

    public static function error(string $key)
    {
        notyf()->addError(__('string.' . $key));
    }

    // new: accept raw/localized message text
    public static function errorMessage(string $message)
    {
        notyf()->addError($message);
    }

    public static function successMessage(string $message)
    {
        notyf()->addSuccess($message);
    }

    // optional
    public static function warning(string $key)
    {
        notyf()->addWarning(__('string.' . $key));
    }

    public static function info(string $key)
    {
        notyf()->addInfo(__('string.' . $key));
    }
}
