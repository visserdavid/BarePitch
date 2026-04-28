-- BarePitch — SQL DDL
-- Version 1.0 — April 2026
-- Target: MySQL 8.0+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS match_rating;
DROP TABLE IF EXISTS penalty_shootout_attempt;
DROP TABLE IF EXISTS match_event;
DROP TABLE IF EXISTS substitution;
DROP TABLE IF EXISTS match_period;
DROP TABLE IF EXISTS match_lineup_slot;
DROP TABLE IF EXISTS match_selection;
DROP TABLE IF EXISTS `match`;
DROP TABLE IF EXISTS formation_position;
DROP TABLE IF EXISTS formation;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS training_focus;
DROP TABLE IF EXISTS training_session;
DROP TABLE IF EXISTS player_season_context;
DROP TABLE IF EXISTS player_profile;
DROP TABLE IF EXISTS player;
DROP TABLE IF EXISTS magic_link;
DROP TABLE IF EXISTS user_team_role;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS team;
DROP TABLE IF EXISTS phase;
DROP TABLE IF EXISTS season;
DROP TABLE IF EXISTS club;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE club (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_club_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE season (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    label VARCHAR(20) NOT NULL,
    starts_on DATE NOT NULL,
    ends_on DATE NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_season_label (label),
    CONSTRAINT chk_season_dates CHECK (starts_on <= ends_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE phase (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    season_id INT UNSIGNED NOT NULL,
    number TINYINT UNSIGNED NOT NULL,
    label VARCHAR(100) NOT NULL,
    focus TEXT NULL,
    starts_on DATE NOT NULL,
    ends_on DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_phase_season_number (season_id, number),
    KEY idx_phase_season_dates (season_id, starts_on, ends_on),
    CONSTRAINT fk_phase_season
        FOREIGN KEY (season_id) REFERENCES season(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_phase_dates CHECK (starts_on <= ends_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE team (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    club_id INT UNSIGNED NOT NULL,
    season_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    based_on_team_id INT UNSIGNED NULL,
    max_match_players TINYINT UNSIGNED NOT NULL DEFAULT 18,
    livestream_hours_after_match TINYINT UNSIGNED NOT NULL DEFAULT 24,
    training_day_1 TINYINT UNSIGNED NULL,
    training_day_2 TINYINT UNSIGNED NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_team_club_season_name (club_id, season_id, name),
    KEY idx_team_club_season (club_id, season_id),
    KEY idx_team_based_on (based_on_team_id),
    CONSTRAINT fk_team_club
        FOREIGN KEY (club_id) REFERENCES club(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_team_season
        FOREIGN KEY (season_id) REFERENCES season(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_team_based_on
        FOREIGN KEY (based_on_team_id) REFERENCES team(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_team_max_match_players CHECK (max_match_players >= 11 AND max_match_players <= 99),
    CONSTRAINT chk_team_livestream_hours CHECK (livestream_hours_after_match >= 1 AND livestream_hours_after_match <= 72),
    CONSTRAINT chk_team_training_day_1 CHECK (training_day_1 IS NULL OR training_day_1 BETWEEN 1 AND 7),
    CONSTRAINT chk_team_training_day_2 CHECK (training_day_2 IS NULL OR training_day_2 BETWEEN 1 AND 7)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user` (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NULL,
    email VARCHAR(255) NOT NULL,
    locale VARCHAR(10) NOT NULL DEFAULT 'en',
    is_administrator TINYINT(1) NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_email (email),
    KEY idx_user_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_team_role (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    team_id INT UNSIGNED NOT NULL,
    role_key VARCHAR(30) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_team_role (user_id, team_id, role_key),
    KEY idx_user_team_role_team (team_id),
    CONSTRAINT fk_user_team_role_user
        FOREIGN KEY (user_id) REFERENCES `user`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_user_team_role_team
        FOREIGN KEY (team_id) REFERENCES team(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_user_team_role_role_key CHECK (role_key IN ('trainer', 'coach', 'team_manager'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE magic_link (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    requested_ip VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_magic_link_token_hash (token_hash),
    KEY idx_magic_link_user (user_id),
    KEY idx_magic_link_expires (expires_at),
    CONSTRAINT fk_magic_link_user
        FOREIGN KEY (user_id) REFERENCES `user`(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE player (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    KEY idx_player_active (active),
    KEY idx_player_deleted_at (deleted_at),
    KEY idx_player_name (first_name, last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE player_profile (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    player_id INT UNSIGNED NOT NULL,
    preferred_foot VARCHAR(10) NULL,
    preferred_line VARCHAR(20) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_player_profile_player (player_id),
    CONSTRAINT fk_player_profile_player
        FOREIGN KEY (player_id) REFERENCES player(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_player_profile_preferred_foot CHECK (preferred_foot IS NULL OR preferred_foot IN ('right', 'left')),
    CONSTRAINT chk_player_profile_preferred_line CHECK (preferred_line IS NULL OR preferred_line IN ('goalkeeper', 'defence', 'midfield', 'attack'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE player_season_context (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    player_id INT UNSIGNED NOT NULL,
    season_id INT UNSIGNED NOT NULL,
    team_id INT UNSIGNED NULL,
    squad_number TINYINT UNSIGNED NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_player_season_context_player_season (player_id, season_id),
    KEY idx_player_season_context_team (team_id),
    KEY idx_player_season_context_season_team (season_id, team_id),
    CONSTRAINT fk_player_season_context_player
        FOREIGN KEY (player_id) REFERENCES player(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_player_season_context_season
        FOREIGN KEY (season_id) REFERENCES season(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_player_season_context_team
        FOREIGN KEY (team_id) REFERENCES team(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE training_session (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    team_id INT UNSIGNED NOT NULL,
    phase_id INT UNSIGNED NOT NULL,
    starts_at DATETIME NOT NULL,
    cancelled TINYINT(1) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_training_session_team_starts_at (team_id, starts_at),
    KEY idx_training_session_phase (phase_id),
    CONSTRAINT fk_training_session_team
        FOREIGN KEY (team_id) REFERENCES team(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_training_session_phase
        FOREIGN KEY (phase_id) REFERENCES phase(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE training_focus (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    training_session_id INT UNSIGNED NOT NULL,
    focus VARCHAR(20) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_training_focus_session_focus (training_session_id, focus),
    CONSTRAINT fk_training_focus_session
        FOREIGN KEY (training_session_id) REFERENCES training_session(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_training_focus_focus CHECK (focus IN ('attacking', 'defending', 'transitioning'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attendance (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    player_id INT UNSIGNED NOT NULL,
    context_type VARCHAR(20) NOT NULL,
    context_id INT UNSIGNED NOT NULL,
    status VARCHAR(10) NOT NULL,
    absence_reason VARCHAR(10) NULL,
    injury_note VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_attendance_player_context (player_id, context_type, context_id),
    KEY idx_attendance_context (context_type, context_id),
    KEY idx_attendance_player (player_id),
    CONSTRAINT fk_attendance_player
        FOREIGN KEY (player_id) REFERENCES player(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_attendance_context_type CHECK (context_type IN ('training_session', 'match')),
    CONSTRAINT chk_attendance_status CHECK (status IN ('present', 'absent', 'injured')),
    CONSTRAINT chk_attendance_absence_reason CHECK (absence_reason IS NULL OR absence_reason IN ('sick', 'holiday', 'school', 'other'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE formation (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    outfield_players TINYINT UNSIGNED NOT NULL DEFAULT 10,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_formation_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE formation_position (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    formation_id INT UNSIGNED NOT NULL,
    position_label VARCHAR(50) NOT NULL,
    line VARCHAR(20) NOT NULL,
    grid_row TINYINT UNSIGNED NOT NULL,
    grid_col TINYINT UNSIGNED NOT NULL,
    display_order TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_formation_position_grid (formation_id, grid_row, grid_col),
    KEY idx_formation_position_formation_order (formation_id, display_order),
    CONSTRAINT fk_formation_position_formation
        FOREIGN KEY (formation_id) REFERENCES formation(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_formation_position_line CHECK (line IN ('goalkeeper', 'defence', 'midfield', 'attack')),
    CONSTRAINT chk_formation_position_grid_row CHECK (grid_row BETWEEN 1 AND 10),
    CONSTRAINT chk_formation_position_grid_col CHECK (grid_col BETWEEN 1 AND 11)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `match` (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    team_id INT UNSIGNED NOT NULL,
    phase_id INT UNSIGNED NOT NULL,
    formation_id INT UNSIGNED NULL,
    date DATE NOT NULL,
    kick_off_time TIME NULL,
    opponent VARCHAR(150) NOT NULL,
    home_away VARCHAR(5) NOT NULL,
    match_type VARCHAR(15) NOT NULL,
    regular_half_duration_minutes TINYINT UNSIGNED NOT NULL DEFAULT 45,
    extra_time_half_duration_minutes TINYINT UNSIGNED NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'planned',
    active_phase VARCHAR(20) NULL,
    goals_scored TINYINT UNSIGNED NOT NULL DEFAULT 0,
    goals_conceded TINYINT UNSIGNED NOT NULL DEFAULT 0,
    shootout_goals_scored TINYINT UNSIGNED NOT NULL DEFAULT 0,
    shootout_goals_conceded TINYINT UNSIGNED NOT NULL DEFAULT 0,
    livestream_token CHAR(64) NULL,
    livestream_started_at TIMESTAMP NULL,
    livestream_expires_at TIMESTAMP NULL,
    livestream_stopped_at TIMESTAMP NULL,
    locked_by_user_id INT UNSIGNED NULL,
    locked_at TIMESTAMP NULL,
    finished_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_match_livestream_token (livestream_token),
    KEY idx_match_team_date (team_id, date),
    KEY idx_match_phase (phase_id),
    KEY idx_match_status (status),
    KEY idx_match_locked_by_user (locked_by_user_id),
    CONSTRAINT fk_match_team
        FOREIGN KEY (team_id) REFERENCES team(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_match_phase
        FOREIGN KEY (phase_id) REFERENCES phase(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_match_formation
        FOREIGN KEY (formation_id) REFERENCES formation(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_match_locked_by_user
        FOREIGN KEY (locked_by_user_id) REFERENCES `user`(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_match_home_away CHECK (home_away IN ('home', 'away')),
    CONSTRAINT chk_match_match_type CHECK (match_type IN ('league', 'tournament', 'friendly')),
    CONSTRAINT chk_match_status CHECK (status IN ('planned', 'prepared', 'active', 'finished')),
    CONSTRAINT chk_match_active_phase CHECK (active_phase IS NULL OR active_phase IN ('regular_time', 'extra_time', 'penalty_shootout'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE match_selection (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id INT UNSIGNED NOT NULL,
    player_id INT UNSIGNED NOT NULL,
    season_context_id INT UNSIGNED NULL,
    is_guest TINYINT(1) NOT NULL DEFAULT 0,
    guest_type VARCHAR(20) NULL,
    shirt_number TINYINT UNSIGNED NULL,
    is_sent_off TINYINT(1) NOT NULL DEFAULT 0,
    sent_off_at_second INT UNSIGNED NULL,
    can_reenter TINYINT(1) NOT NULL DEFAULT 1,
    playing_time_seconds INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_match_selection_match_player (match_id, player_id),
    KEY idx_match_selection_context (season_context_id),
    KEY idx_match_selection_match_guest (match_id, is_guest),
    CONSTRAINT fk_match_selection_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_match_selection_player
        FOREIGN KEY (player_id) REFERENCES player(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_match_selection_context
        FOREIGN KEY (season_context_id) REFERENCES player_season_context(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_match_selection_guest_type CHECK (guest_type IS NULL OR guest_type IN ('internal', 'external'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE match_lineup_slot (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id INT UNSIGNED NOT NULL,
    match_selection_id INT UNSIGNED NOT NULL,
    grid_row TINYINT UNSIGNED NULL,
    grid_col TINYINT UNSIGNED NULL,
    is_starting_lineup TINYINT(1) NOT NULL DEFAULT 0,
    is_active_on_field TINYINT(1) NOT NULL DEFAULT 0,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_match_lineup_slot_match_selection (match_id, match_selection_id),
    KEY idx_match_lineup_slot_match_grid (match_id, grid_row, grid_col),
    CONSTRAINT fk_match_lineup_slot_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_match_lineup_slot_selection
        FOREIGN KEY (match_selection_id) REFERENCES match_selection(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_match_lineup_slot_grid_row CHECK (grid_row IS NULL OR grid_row BETWEEN 1 AND 10),
    CONSTRAINT chk_match_lineup_slot_grid_col CHECK (grid_col IS NULL OR grid_col BETWEEN 1 AND 11)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE match_period (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id INT UNSIGNED NOT NULL,
    phase_type VARCHAR(20) NOT NULL,
    period_number TINYINT UNSIGNED NOT NULL,
    started_at TIMESTAMP NULL,
    ended_at TIMESTAMP NULL,
    duration_minutes TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_match_period_match_phase_period (match_id, phase_type, period_number),
    CONSTRAINT fk_match_period_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_match_period_phase_type CHECK (phase_type IN ('regular', 'extra_time')),
    CONSTRAINT chk_match_period_period_number CHECK (period_number IN (1, 2))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE substitution (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id INT UNSIGNED NOT NULL,
    period_id INT UNSIGNED NOT NULL,
    match_second INT UNSIGNED NOT NULL,
    player_off_selection_id INT UNSIGNED NOT NULL,
    player_on_selection_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_substitution_match_second (match_id, match_second),
    CONSTRAINT fk_substitution_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_substitution_period
        FOREIGN KEY (period_id) REFERENCES match_period(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_substitution_player_off
        FOREIGN KEY (player_off_selection_id) REFERENCES match_selection(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_substitution_player_on
        FOREIGN KEY (player_on_selection_id) REFERENCES match_selection(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE match_event (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id INT UNSIGNED NOT NULL,
    period_id INT UNSIGNED NULL,
    match_second INT UNSIGNED NOT NULL,
    team_side VARCHAR(10) NOT NULL,
    event_type VARCHAR(15) NOT NULL,
    player_selection_id INT UNSIGNED NULL,
    assist_selection_id INT UNSIGNED NULL,
    penalty_outcome VARCHAR(10) NULL,
    zone_code CHAR(2) NULL,
    note_text TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_match_event_match_second (match_id, match_second),
    KEY idx_match_event_period (period_id),
    KEY idx_match_event_player (player_selection_id),
    CONSTRAINT fk_match_event_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_match_event_period
        FOREIGN KEY (period_id) REFERENCES match_period(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_match_event_player
        FOREIGN KEY (player_selection_id) REFERENCES match_selection(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_match_event_assist
        FOREIGN KEY (assist_selection_id) REFERENCES match_selection(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_match_event_team_side CHECK (team_side IN ('own', 'opponent')),
    CONSTRAINT chk_match_event_event_type CHECK (event_type IN ('goal', 'penalty', 'yellow_card', 'red_card', 'note')),
    CONSTRAINT chk_match_event_penalty_outcome CHECK (penalty_outcome IS NULL OR penalty_outcome IN ('scored', 'missed')),
    CONSTRAINT chk_match_event_zone_code CHECK (zone_code IS NULL OR zone_code IN ('tl', 'tm', 'tr', 'ml', 'mm', 'mr', 'bl', 'bm', 'br'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE penalty_shootout_attempt (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id INT UNSIGNED NOT NULL,
    attempt_order TINYINT UNSIGNED NOT NULL,
    round_number TINYINT UNSIGNED NOT NULL,
    team_side VARCHAR(10) NOT NULL,
    player_selection_id INT UNSIGNED NULL,
    player_name_text VARCHAR(150) NULL,
    outcome VARCHAR(10) NOT NULL,
    zone_code CHAR(2) NULL,
    is_sudden_death TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_penalty_shootout_attempt_match_order (match_id, attempt_order),
    KEY idx_penalty_shootout_attempt_match_round (match_id, round_number),
    CONSTRAINT fk_penalty_shootout_attempt_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_penalty_shootout_attempt_player
        FOREIGN KEY (player_selection_id) REFERENCES match_selection(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_penalty_shootout_attempt_team_side CHECK (team_side IN ('own', 'opponent')),
    CONSTRAINT chk_penalty_shootout_attempt_outcome CHECK (outcome IN ('scored', 'missed')),
    CONSTRAINT chk_penalty_shootout_attempt_zone_code CHECK (zone_code IS NULL OR zone_code IN ('tl', 'tm', 'tr', 'ml', 'mm', 'mr', 'bl', 'bm', 'br'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE match_rating (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id INT UNSIGNED NOT NULL,
    player_selection_id INT UNSIGNED NOT NULL,
    pace TINYINT UNSIGNED NULL,
    shooting TINYINT UNSIGNED NULL,
    passing TINYINT UNSIGNED NULL,
    dribbling TINYINT UNSIGNED NULL,
    defending TINYINT UNSIGNED NULL,
    physicality TINYINT UNSIGNED NULL,
    is_complete TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_match_rating_match_player (match_id, player_selection_id),
    CONSTRAINT fk_match_rating_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_match_rating_player
        FOREIGN KEY (player_selection_id) REFERENCES match_selection(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_match_rating_pace CHECK (pace IS NULL OR pace BETWEEN 1 AND 5),
    CONSTRAINT chk_match_rating_shooting CHECK (shooting IS NULL OR shooting BETWEEN 1 AND 5),
    CONSTRAINT chk_match_rating_passing CHECK (passing IS NULL OR passing BETWEEN 1 AND 5),
    CONSTRAINT chk_match_rating_dribbling CHECK (dribbling IS NULL OR dribbling BETWEEN 1 AND 5),
    CONSTRAINT chk_match_rating_defending CHECK (defending IS NULL OR defending BETWEEN 1 AND 5),
    CONSTRAINT chk_match_rating_physicality CHECK (physicality IS NULL OR physicality BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_log (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT UNSIGNED NOT NULL,
    match_id INT UNSIGNED NULL,
    changed_by_user_id INT UNSIGNED NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_log_entity (entity_type, entity_id),
    KEY idx_audit_log_match (match_id),
    KEY idx_audit_log_changed_by (changed_by_user_id),
    CONSTRAINT fk_audit_log_match
        FOREIGN KEY (match_id) REFERENCES `match`(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_audit_log_changed_by
        FOREIGN KEY (changed_by_user_id) REFERENCES `user`(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_audit_log_entity_type CHECK (entity_type IN ('match', 'match_event', 'match_lineup_slot', 'substitution', 'penalty_shootout_attempt'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
