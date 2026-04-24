<?php

declare(strict_types=1);

class TeamService
{
    public function validateTeamInput(array $post): array
    {
        $errors = [];

        $name = trim($post['name'] ?? '');
        if ($name === '') {
            $errors['name'] = __('validation.required');
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = __('validation.too_long');
        }

        $season = trim($post['season'] ?? '');
        if ($season !== '' && mb_strlen($season) > 20) {
            $errors['season'] = __('validation.too_long');
        }

        return $errors;
    }
}
