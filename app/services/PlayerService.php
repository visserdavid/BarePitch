<?php

declare(strict_types=1);

class PlayerService
{
    public function validatePlayerInput(array $post): array
    {
        $errors = [];

        $displayName = trim($post['display_name'] ?? '');
        if ($displayName === '') {
            $errors['display_name'] = __('validation.required');
        } elseif (mb_strlen($displayName) > 100) {
            $errors['display_name'] = __('validation.too_long');
        }

        $shirtNumber = trim($post['shirt_number'] ?? '');
        if ($shirtNumber !== '') {
            $num = filter_var($shirtNumber, FILTER_VALIDATE_INT);
            if ($num === false || $num < 1 || $num > 99) {
                $errors['shirt_number'] = __('validation.shirt_number_range');
            }
        }

        return $errors;
    }
}
