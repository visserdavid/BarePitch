CREATE TABLE match_players (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    match_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,
    status ENUM('unknown', 'available', 'unavailable', 'selected', 'present', 'absent') NOT NULL DEFAULT 'unknown',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    CONSTRAINT fk_match_players_match
        FOREIGN KEY (match_id)
        REFERENCES matches(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_match_players_player
        FOREIGN KEY (player_id)
        REFERENCES players(id)
        ON DELETE RESTRICT,

    CONSTRAINT uq_match_players_match_player
        UNIQUE (match_id, player_id),

    INDEX idx_match_players_match_id (match_id),
    INDEX idx_match_players_player_id (player_id),
    INDEX idx_match_players_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
