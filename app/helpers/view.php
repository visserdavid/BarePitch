<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function render(string $view, array $data = []): void
{
    $path = dirname(__DIR__, 1) . '/views/' . $view . '.php';
    extract($data, EXTR_SKIP);
    include $path;
}
