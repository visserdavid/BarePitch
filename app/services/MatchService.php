<?php

declare(strict_types=1);

class MatchService
{
    public function validateMatchInput(array $post): array
    {
        $errors = [];

        $opponentName = trim($post['opponent_name'] ?? '');
        if ($opponentName === '') {
            $errors['opponent_name'] = __('validation.required');
        } elseif (mb_strlen($opponentName) > 100) {
            $errors['opponent_name'] = __('validation.too_long');
        }

        $matchDate = trim($post['match_date'] ?? '');
        if ($matchDate === '') {
            $errors['match_date'] = __('validation.required');
        } else {
            $parsed = DateTime::createFromFormat('Y-m-d', $matchDate);
            if (!$parsed || $parsed->format('Y-m-d') !== $matchDate) {
                $errors['match_date'] = __('validation.invalid_date');
            }
        }

        $kickoffTime = trim($post['kickoff_time'] ?? '');
        if ($kickoffTime !== '') {
            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $kickoffTime)) {
                $errors['kickoff_time'] = __('validation.invalid_time');
            }
        }

        $location = trim($post['location'] ?? '');
        if ($location !== '' && mb_strlen($location) > 150) {
            $errors['location'] = __('validation.too_long');
        }

        $homeAway = trim($post['home_away'] ?? '');
        if ($homeAway !== '' && !in_array($homeAway, ['home', 'away', 'neutral'], true)) {
            $errors['home_away'] = __('validation.invalid_status');
        }

        return $errors;
    }
}
