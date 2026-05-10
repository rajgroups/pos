<?php

namespace App\Helpers;

class KeywordHelper
{
    // Response status
    public const STATUS  = 'status';
    public const SUCCESS = 'success';
    public const ERROR   = 'error';

    // Response keys
    public const MESSAGE = 'message';
    public const ERRORS  = 'errors';
    public const DATA    = 'data';

    // Keyword
    public const BOOLEAN = 'boolean';
    /**
     * Auto-translation from string.php (lang file)
     * Example: KeywordHelper::text('category.created_success')
     */
    public static function text(string $key): string
    {
        return __('string.' . $key);
    }
}
