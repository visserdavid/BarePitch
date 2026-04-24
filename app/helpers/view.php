<?php

declare(strict_types=1);

function render(string $view, array $data = []): void
{
    $path = dirname(__DIR__, 1) . '/views/' . $view . '.php';
    extract($data, EXTR_SKIP);
    include $path;
}
