<?php

declare(strict_types=1);

class TeamModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPdo();
    }

    public function findAllForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT t.*,
                    COUNT(DISTINCT p.id) AS player_count,
                    COUNT(DISTINCT m.id) AS match_count
             FROM teams t
             LEFT JOIN players p ON p.team_id = t.id AND p.deleted_at IS NULL
             LEFT JOIN matches m ON m.team_id = t.id AND m.deleted_at IS NULL
             WHERE t.user_id = ?
               AND t.status = \'active\'
               AND t.deleted_at IS NULL
             GROUP BY t.id
             ORDER BY t.name ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findForUser(int $teamId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM teams
             WHERE id = ? AND user_id = ? AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$teamId, $userId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function create(int $userId, string $name, ?string $season): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO teams (user_id, name, season) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $name, $season]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $teamId, int $userId, string $name, ?string $season): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE teams SET name = ?, season = ?
             WHERE id = ? AND user_id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$name, $season, $teamId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function archive(int $teamId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE teams SET status = \'archived\'
             WHERE id = ? AND user_id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$teamId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
