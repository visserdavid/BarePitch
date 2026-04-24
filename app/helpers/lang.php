<?php

declare(strict_types=1);

function __lang_strings(): array
{
    static $strings = null;

    if ($strings === null) {
        $locale = getenv('APP_LANG') ?: 'en';
        $file   = dirname(__DIR__, 2) . "/lang/{$locale}.php";

        if (!file_exists($file)) {
            $file = dirname(__DIR__, 2) . '/lang/en.php';
        }

        $strings = require $file;
    }

    return $strings;
}

function __(string $key, array $replace = []): string
{
    $strings = __lang_strings();
    $value   = $strings[$key] ?? $key;

    foreach ($replace as $placeholder => $replacement) {
        $value = str_replace(':' . $placeholder, $replacement, $value);
    }

    return $value;
}
