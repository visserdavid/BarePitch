<?php

declare(strict_types=1);

class AttendanceModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPdo();
    }

    public function findForMatch(int $matchId, int $teamId, int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                 p.id,
                 p.display_name,
                 p.shirt_number,
                 COALESCE(mp.status, \'unknown\') AS match_status
             FROM players p
             LEFT JOIN match_players mp
                 ON mp.player_id = p.id
                 AND mp.match_id = ?
             WHERE p.team_id = ?
               AND p.status = \'active\'
               AND p.deleted_at IS NULL
             ORDER BY p.shirt_number IS NULL ASC, p.shirt_number ASC, p.display_name ASC'
        );
        $stmt->execute([$matchId, $teamId]);
        return $stmt->fetchAll();
    }

    public function saveStatuses(int $matchId, int $teamId, int $userId, array $statuses): bool
    {
        $allowedStatuses = ['unknown', 'available', 'unavailable', 'selected'];

        $stmt = $this->pdo->prepare(
            'INSERT INTO match_players (match_id, player_id, status)
             SELECT ?, p.id, ?
             FROM players p
             WHERE p.id = ?
               AND p.team_id = ?
               AND p.status = \'active\'
               AND p.deleted_at IS NULL
             ON DUPLICATE KEY UPDATE
                 status = VALUES(status),
                 updated_at = CURRENT_TIMESTAMP'
        );

        foreach ($statuses as $playerId => $status) {
            if (!ctype_digit((string) $playerId) || (int) $playerId <= 0) {
                continue;
            }
            if (!in_array($status, $allowedStatuses, true)) {
                continue;
            }
            $stmt->execute([$matchId, $status, (int) $playerId, $teamId]);
        }

        return true;
    }

    public function getStatusSummary(int $matchId): array
    {
        $summary = ['unknown' => 0, 'available' => 0, 'unavailable' => 0, 'selected' => 0];

        $stmt = $this->pdo->prepare(
            'SELECT status, COUNT(*) AS cnt
             FROM match_players
             WHERE match_id = ?
               AND status IN (\'unknown\', \'available\', \'unavailable\', \'selected\')
             GROUP BY status'
        );
        $stmt->execute([$matchId]);

        foreach ($stmt->fetchAll() as $row) {
            $summary[$row['status']] = (int) $row['cnt'];
        }

        return $summary;
    }
}
