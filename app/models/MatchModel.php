<?php

declare(strict_types=1);

class MatchModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPdo();
    }

    public function findAllForTeam(int $teamId, int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT m.*
             FROM matches m
             JOIN teams t ON t.id = m.team_id
             WHERE m.team_id = ?
               AND t.user_id = ?
               AND m.deleted_at IS NULL
               AND m.status != \'archived\'
             ORDER BY (m.match_date >= CURDATE()) DESC, m.match_date DESC, m.kickoff_time ASC'
        );
        $stmt->execute([$teamId, $userId]);
        return $stmt->fetchAll();
    }

    public function findUpcomingForTeam(int $teamId, int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT m.*
             FROM matches m
             JOIN teams t ON t.id = m.team_id
             WHERE m.team_id = ?
               AND t.user_id = ?
               AND m.match_date >= CURDATE()
               AND m.status = \'planned\'
               AND m.deleted_at IS NULL
             ORDER BY m.match_date ASC, m.kickoff_time ASC'
        );
        $stmt->execute([$teamId, $userId]);
        return $stmt->fetchAll();
    }

    public function findPastForTeam(int $teamId, int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT m.*
             FROM matches m
             JOIN teams t ON t.id = m.team_id
             WHERE m.team_id = ?
               AND t.user_id = ?
               AND m.match_date < CURDATE()
               AND m.deleted_at IS NULL
               AND m.status != \'archived\'
             ORDER BY m.match_date DESC'
        );
        $stmt->execute([$teamId, $userId]);
        return $stmt->fetchAll();
    }

    public function findForTeam(int $matchId, int $teamId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT m.*
             FROM matches m
             JOIN teams t ON t.id = m.team_id
             WHERE m.id = ?
               AND m.team_id = ?
               AND t.user_id = ?
               AND m.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$matchId, $teamId, $userId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function create(int $teamId, string $opponentName, string $matchDate, ?string $kickoffTime, ?string $location, ?string $homeAway): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO matches (team_id, opponent_name, match_date, kickoff_time, location, home_away)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$teamId, $opponentName, $matchDate, $kickoffTime, $location, $homeAway]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $matchId, int $teamId, int $userId, string $opponentName, string $matchDate, ?string $kickoffTime, ?string $location, ?string $homeAway): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE matches m
             JOIN teams t ON t.id = m.team_id
             SET m.opponent_name = ?, m.match_date = ?, m.kickoff_time = ?, m.location = ?, m.home_away = ?
             WHERE m.id = ?
               AND m.team_id = ?
               AND t.user_id = ?
               AND m.deleted_at IS NULL'
        );
        $stmt->execute([$opponentName, $matchDate, $kickoffTime, $location, $homeAway, $matchId, $teamId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function archive(int $matchId, int $teamId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE matches m
             JOIN teams t ON t.id = m.team_id
             SET m.status = \'archived\'
             WHERE m.id = ?
               AND m.team_id = ?
               AND t.user_id = ?
               AND m.deleted_at IS NULL'
        );
        $stmt->execute([$matchId, $teamId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
