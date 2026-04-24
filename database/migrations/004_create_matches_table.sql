CREATE TABLE matches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id INT UNSIGNED NOT NULL,
    opponent_name VARCHAR(100) NOT NULL,
    match_date DATE NOT NULL,
    kickoff_time TIME NULL,
    location VARCHAR(150) NULL,
    home_away ENUM('home', 'away', 'neutral') NULL,
    status ENUM('planned', 'completed', 'archived') NOT NULL DEFAULT 'planned',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,

    CONSTRAINT fk_matches_team
        FOREIGN KEY (team_id)
        REFERENCES teams(id)
        ON DELETE CASCADE,

    INDEX idx_matches_team_id (team_id),
    INDEX idx_matches_match_date (match_date),
    INDEX idx_matches_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
