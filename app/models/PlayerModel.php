<?php

declare(strict_types=1);

class PlayerModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPdo();
    }

    public function findAllForTeam(int $teamId, int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*
             FROM players p
             JOIN teams t ON t.id = p.team_id
             WHERE p.team_id = ?
               AND t.user_id = ?
               AND p.deleted_at IS NULL
             ORDER BY p.shirt_number IS NULL ASC, p.shirt_number ASC, p.display_name ASC'
        );
        $stmt->execute([$teamId, $userId]);
        return $stmt->fetchAll();
    }

    public function findActiveForTeam(int $teamId, int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*
             FROM players p
             JOIN teams t ON t.id = p.team_id
             WHERE p.team_id = ?
               AND t.user_id = ?
               AND p.deleted_at IS NULL
               AND p.status = \'active\'
             ORDER BY p.shirt_number IS NULL ASC, p.shirt_number ASC, p.display_name ASC'
        );
        $stmt->execute([$teamId, $userId]);
        return $stmt->fetchAll();
    }

    public function findForTeam(int $playerId, int $teamId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*
             FROM players p
             JOIN teams t ON t.id = p.team_id
             WHERE p.id = ?
               AND p.team_id = ?
               AND t.user_id = ?
               AND p.deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute([$playerId, $teamId, $userId]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public function create(int $teamId, string $displayName, ?int $shirtNumber): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO players (team_id, display_name, shirt_number) VALUES (?, ?, ?)'
        );
        $stmt->execute([$teamId, $displayName, $shirtNumber]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $playerId, int $teamId, int $userId, string $displayName, ?int $shirtNumber): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE players p
             JOIN teams t ON t.id = p.team_id
             SET p.display_name = ?, p.shirt_number = ?
             WHERE p.id = ?
               AND p.team_id = ?
               AND t.user_id = ?
               AND p.deleted_at IS NULL'
        );
        $stmt->execute([$displayName, $shirtNumber, $playerId, $teamId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function setInactive(int $playerId, int $teamId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE players p
             JOIN teams t ON t.id = p.team_id
             SET p.status = \'inactive\'
             WHERE p.id = ?
               AND p.team_id = ?
               AND t.user_id = ?
               AND p.deleted_at IS NULL'
        );
        $stmt->execute([$playerId, $teamId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function hasMatchHistory(int $playerId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM match_players WHERE player_id = ? LIMIT 1'
        );
        $stmt->execute([$playerId]);
        return $stmt->fetch() !== false;
    }

    public function delete(int $playerId, int $teamId, int $userId): bool
    {
        if ($this->hasMatchHistory($playerId)) {
            $this->setInactive($playerId, $teamId, $userId);
            return false;
        }

        $stmt = $this->pdo->prepare(
            'DELETE p FROM players p
             JOIN teams t ON t.id = p.team_id
             WHERE p.id = ?
               AND p.team_id = ?
               AND t.user_id = ?'
        );
        $stmt->execute([$playerId, $teamId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
