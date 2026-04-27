-- =============================================================
-- BarePitch – Development Seed Data
-- =============================================================
-- For local development only. Never use in production.
-- All data is entirely fictional.
--
-- Users and passwords:
--   coach_a@example.test  /  CoachA2026!
--   coach_b@example.test  /  CoachB2026!
--   coach_c@example.test  /  CoachC2026!
--
-- Usage:
--   mysql -u barepitch_user -p barepitch_local < database/seeds/dev_seed.sql
--
-- Run AFTER all migrations (001–005) have been applied.
-- Safe to re-run: DELETE statements clean up existing seed data first.
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM match_players;
DELETE FROM matches;
DELETE FROM players;
DELETE FROM teams;
DELETE FROM users;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- USERS
-- =============================================================
-- coach_a: two active teams (main test user)
-- coach_b: one active team, one archived team
-- coach_c: one team, minimal data (edge case testing)

INSERT INTO users (id, email, password_hash, display_name, created_at) VALUES
(1, 'coach_a@example.test', '$2y$10$qFxWQ7A8WFLuCud3GZF7D.l5PJOHGul3G5jp.N1nRzvKU7vgQjxE2', 'Erik Vos',    '2026-01-15 09:00:00'),
(2, 'coach_b@example.test', '$2y$10$sgiCiGzFz8./7oDu7/3FmuyQ.OvR4YMelByexG45hsrxd4a7Bhvt2', 'Sandra Brink', '2026-01-20 11:30:00'),
(3, 'coach_c@example.test', '$2y$10$4WfygR1ai9BUfTR3rWLcneYFXUHUWa/3.ybIWGiCGvJOMBb.mFOXm', 'Thomas Maat',  '2026-02-03 14:00:00');

-- =============================================================
-- TEAMS
-- =============================================================
-- coach_a owns: FC Noordveld O15 (active), FC Noordveld O17 (active)
-- coach_b owns: VV Ridderhoek A1 (active), VV Ridderhoek B2 (archived)
-- coach_c owns: SC Dalstein JO13 (active)

INSERT INTO teams (id, user_id, name, season, status, created_at) VALUES
(1, 1, 'FC Noordveld O15',     '2025/2026', 'active',   '2026-01-15 09:05:00'),
(2, 1, 'FC Noordveld O17',     '2025/2026', 'active',   '2026-01-15 09:10:00'),
(3, 2, 'VV Ridderhoek A1',     '2025/2026', 'active',   '2026-01-20 11:35:00'),
(4, 2, 'VV Ridderhoek B2',     '2024/2025', 'archived', '2025-08-01 10:00:00'),
(5, 3, 'SC Dalstein JO13',     '2025/2026', 'active',   '2026-02-03 14:05:00');

-- =============================================================
-- PLAYERS
-- =============================================================

-- FC Noordveld O15 (team 1) — 16 players, mix of active/inactive
INSERT INTO players (id, team_id, display_name, shirt_number, status, created_at) VALUES
( 1,  1, 'Lars Dekker',      1,    'active',   '2026-01-15 09:15:00'),
( 2,  1, 'Joost van Dam',    2,    'active',   '2026-01-15 09:15:00'),
( 3,  1, 'Niek Houten',      3,    'active',   '2026-01-15 09:15:00'),
( 4,  1, 'Finn Bos',         4,    'active',   '2026-01-15 09:15:00'),
( 5,  1, 'Ruben Smeets',     5,    'active',   '2026-01-15 09:15:00'),
( 6,  1, 'Daan Kuiper',      6,    'active',   '2026-01-15 09:15:00'),
( 7,  1, 'Sven Willems',     7,    'active',   '2026-01-15 09:15:00'),
( 8,  1, 'Max de Groot',     8,    'active',   '2026-01-15 09:15:00'),
( 9,  1, 'Tim Verhoef',      9,    'active',   '2026-01-15 09:15:00'),
(10,  1, 'Jesse Brands',     10,   'active',   '2026-01-15 09:15:00'),
(11,  1, 'Bram Linden',      11,   'active',   '2026-01-15 09:15:00'),
(12,  1, 'Koen Visser',      13,   'active',   '2026-01-15 09:15:00'),
(13,  1, 'Thijs Molenaar',   14,   'active',   '2026-01-15 09:15:00'),
(14,  1, 'Pieter van Loon',  17,   'active',   '2026-02-01 10:00:00'),
(15,  1, 'Sam Hendriks',     NULL, 'active',   '2026-02-01 10:00:00'),
(16,  1, 'Owen Bakker',      22,   'inactive', '2026-01-15 09:15:00'); -- geblesseerd

-- FC Noordveld O17 (team 2) — 14 players
INSERT INTO players (id, team_id, display_name, shirt_number, status, created_at) VALUES
(17,  2, 'Aiden de Vries',   1,    'active',   '2026-01-15 09:20:00'),
(18,  2, 'Luca Prins',       3,    'active',   '2026-01-15 09:20:00'),
(19,  2, 'Noah Jacobs',      4,    'active',   '2026-01-15 09:20:00'),
(20,  2, 'Julian Smit',      5,    'active',   '2026-01-15 09:20:00'),
(21,  2, 'Milan Hermans',    6,    'active',   '2026-01-15 09:20:00'),
(22,  2, 'Dylan Postma',     7,    'active',   '2026-01-15 09:20:00'),
(23,  2, 'Robin van Berg',   8,    'active',   '2026-01-15 09:20:00'),
(24,  2, 'Bas Groen',        9,    'active',   '2026-01-15 09:20:00'),
(25,  2, 'Floris Bosman',    10,   'active',   '2026-01-15 09:20:00'),
(26,  2, 'Stef Veenstra',    11,   'active',   '2026-01-15 09:20:00'),
(27,  2, 'Niels Timmerman',  14,   'active',   '2026-01-15 09:20:00'),
(28,  2, 'Cas Dijkstra',     NULL, 'active',   '2026-03-01 08:00:00'),
(29,  2, 'Victor Blom',      16,   'active',   '2026-03-01 08:00:00'),
(30,  2, 'Rick Oosterhout',  18,   'inactive', '2026-01-15 09:20:00');

-- VV Ridderhoek A1 (team 3) — 13 players
INSERT INTO players (id, team_id, display_name, shirt_number, status, created_at) VALUES
(31,  3, 'Kevin Adriaans',   1,    'active',   '2026-01-20 11:40:00'),
(32,  3, 'Arno Huisman',     2,    'active',   '2026-01-20 11:40:00'),
(33,  3, 'Dennis Peeters',   3,    'active',   '2026-01-20 11:40:00'),
(34,  3, 'Geert Wolters',    5,    'active',   '2026-01-20 11:40:00'),
(35,  3, 'Mark Kuijpers',    6,    'active',   '2026-01-20 11:40:00'),
(36,  3, 'Peter Janssen',    7,    'active',   '2026-01-20 11:40:00'),
(37,  3, 'Rik Vermeulen',    8,    'active',   '2026-01-20 11:40:00'),
(38,  3, 'Bert Sloot',       9,    'active',   '2026-01-20 11:40:00'),
(39,  3, 'Frank de Boer',    10,   'active',   '2026-01-20 11:40:00'),
(40,  3, 'Hans Kroese',      11,   'active',   '2026-01-20 11:40:00'),
(41,  3, 'Wim Nooij',        NULL, 'active',   '2026-01-20 11:40:00'),
(42,  3, 'Ad Verburg',       15,   'active',   '2026-02-10 09:00:00'),
(43,  3, 'Cor Reinders',     17,   'inactive', '2026-01-20 11:40:00');

-- SC Dalstein JO13 (team 5) — 12 players
INSERT INTO players (id, team_id, display_name, shirt_number, status, created_at) VALUES
(44,  5, 'Mats de Ruiter',   1,    'active',   '2026-02-03 14:10:00'),
(45,  5, 'Sem Hubers',       2,    'active',   '2026-02-03 14:10:00'),
(46,  5, 'Tijs Koers',       3,    'active',   '2026-02-03 14:10:00'),
(47,  5, 'Bo Schippers',     4,    'active',   '2026-02-03 14:10:00'),
(48,  5, 'Roel van Dijk',    5,    'active',   '2026-02-03 14:10:00'),
(49,  5, 'Stijn Bouwman',    6,    'active',   '2026-02-03 14:10:00'),
(50,  5, 'Luuk Vermeer',     7,    'active',   '2026-02-03 14:10:00'),
(51,  5, 'Dean Kok',         9,    'active',   '2026-02-03 14:10:00'),
(52,  5, 'Lev Boer',         10,   'active',   '2026-02-03 14:10:00'),
(53,  5, 'Axel Prins',       11,   'active',   '2026-02-03 14:10:00'),
(54,  5, 'Tobias Hoek',      NULL, 'active',   '2026-02-03 14:10:00'),
(55,  5, 'Pepijn Vos',       13,   'active',   '2026-02-03 14:10:00');

-- =============================================================
-- MATCHES
-- =============================================================
-- Mix van: aankomend, recent gespeeld, en gearchiveerd
-- Gegeven dat de huidige datum ~ april/mei 2026 is

-- FC Noordveld O15 (team 1)
INSERT INTO matches (id, team_id, opponent_name, match_date, kickoff_time, location, home_away, status, created_at) VALUES
( 1,  1, 'VV Sportlust O15',    '2026-02-08', '10:00:00', 'Sportpark Noord veld 2', 'home',    'planned', '2026-02-01 08:00:00'),
( 2,  1, 'SC Rivierstaete O15', '2026-02-15', '13:30:00', NULL,                     'away',    'planned', '2026-02-01 08:00:00'),
( 3,  1, 'FC Waterland O15',    '2026-02-22', '10:00:00', 'Sportpark Noord veld 2', 'home',    'planned', '2026-02-01 08:00:00'),
( 4,  1, 'DSV O15',             '2026-03-01', '11:00:00', 'Accommodatie DSV',       'away',    'planned', '2026-02-01 08:00:00'),
( 5,  1, 'BFC O15',             '2026-03-15', '10:00:00', 'Sportpark Noord veld 2', 'home',    'planned', '2026-03-01 08:00:00'),
( 6,  1, 'SV Merenburgh O15',   '2026-03-22', '14:00:00', 'Sportcomplex Meren',     'away',    'planned', '2026-03-01 08:00:00'),
( 7,  1, 'VV Sportlust O15',    '2026-04-05', '10:00:00', NULL,                     'away',    'planned', '2026-03-15 08:00:00'),
( 8,  1, 'FC Waterland O15',    '2026-04-12', '11:00:00', 'Sportpark Noord veld 2', 'home',    'planned', '2026-03-15 08:00:00'),
( 9,  1, 'SC Rivierstaete O15', '2026-04-19', '10:00:00', 'Sportpark Noord veld 2', 'home',    'planned', '2026-04-01 08:00:00'),
(10,  1, 'DSV O15',             '2026-05-03', '13:00:00', 'Sportpark Noord veld 2', 'home',    'planned', '2026-04-01 08:00:00'),
(11,  1, 'BFC O15',             '2026-05-10', '10:00:00', NULL,                     'away',    'planned', '2026-04-15 08:00:00'),
(12,  1, 'SV Merenburgh O15',   '2026-05-17', '11:00:00', 'Sportpark Noord veld 2', 'home',    'planned', '2026-04-15 08:00:00'),
-- Gearchiveerde wedstrijd (vorig seizoen)
(13,  1, 'FC Testclub O15',     '2025-11-09', '10:00:00', 'Sportpark Noord veld 2', 'home',    'archived','2025-11-01 08:00:00');

-- FC Noordveld O17 (team 2)
INSERT INTO matches (id, team_id, opponent_name, match_date, kickoff_time, location, home_away, status, created_at) VALUES
(14,  2, 'Ajax O17 B',          '2026-03-08', '14:00:00', 'De Toekomst veld 4',     'away',    'planned', '2026-03-01 08:00:00'),
(15,  2, 'VV Zuidhoek O17',     '2026-03-22', '11:00:00', 'Sportpark Noord veld 1', 'home',    'planned', '2026-03-01 08:00:00'),
(16,  2, 'SC Berghem O17',      '2026-04-05', '10:30:00', 'Sportcomplex Berghem',   'away',    'planned', '2026-03-15 08:00:00'),
(17,  2, 'VV Oostrand O17',     '2026-04-19', '11:00:00', 'Sportpark Noord veld 1', 'home',    'planned', '2026-04-01 08:00:00'),
(18,  2, 'SC Berghem O17',      '2026-05-03', '13:30:00', 'Sportpark Noord veld 1', 'home',    'planned', '2026-04-15 08:00:00'),
(19,  2, 'Ajax O17 B',          '2026-05-17', '14:00:00', 'Sportpark Noord veld 1', 'home',    'planned', '2026-04-15 08:00:00');

-- VV Ridderhoek A1 (team 3)
INSERT INTO matches (id, team_id, opponent_name, match_date, kickoff_time, location, home_away, status, created_at) VALUES
(20,  3, 'CSV Zandvoort',       '2026-02-14', '14:30:00', 'Sportpark Ridderhoek',   'home',    'planned', '2026-02-01 08:00:00'),
(21,  3, 'FC Westhoek A1',      '2026-02-28', '15:00:00', 'De Westhoek complex',    'away',    'planned', '2026-02-01 08:00:00'),
(22,  3, 'SV Dijkveld A1',      '2026-03-14', '14:00:00', 'Sportpark Ridderhoek',   'home',    'planned', '2026-03-01 08:00:00'),
(23,  3, 'RKC Amateur A1',      '2026-03-28', '14:30:00', 'Accommodatie RKC',       'away',    'planned', '2026-03-01 08:00:00'),
(24,  3, 'CSV Zandvoort',       '2026-04-11', '15:00:00', NULL,                     'away',    'planned', '2026-04-01 08:00:00'),
(25,  3, 'FC Westhoek A1',      '2026-04-25', '14:00:00', 'Sportpark Ridderhoek',   'home',    'planned', '2026-04-01 08:00:00'),
(26,  3, 'SV Dijkveld A1',      '2026-05-09', '14:30:00', NULL,                     'away',    'planned', '2026-04-15 08:00:00');

-- SC Dalstein JO13 (team 5)
INSERT INTO matches (id, team_id, opponent_name, match_date, kickoff_time, location, home_away, status, created_at) VALUES
(27,  5, 'FC Dalstein JO13',    '2026-04-26', '10:00:00', 'Sportpark Dalstein',     'home',    'planned', '2026-04-01 08:00:00'),
(28,  5, 'VV Zonneberg JO13',   '2026-05-10', '10:00:00', 'Complex Zonneberg',      'away',    'planned', '2026-04-01 08:00:00'),
(29,  5, 'SC Rivierstaete JO13','2026-05-24', '11:00:00', 'Sportpark Dalstein',     'home',    'planned', '2026-04-15 08:00:00');

-- =============================================================
-- MATCH PLAYERS (attendance)
-- =============================================================
-- Gevuld voor een selectie van wedstrijden om realistische testscenarios te bieden:
--   - Wedstrijd met volledig ingevulde aanwezigheid + selectie
--   - Wedstrijd met gedeeltelijke aanwezigheid
--   - Wedstrijd zonder aanwezigheid (leeg)

-- Wedstrijd 1: FC Noordveld O15 vs VV Sportlust (volledig ingevuld)
INSERT INTO match_players (match_id, player_id, status) VALUES
( 1,  1, 'selected'),    -- Lars Dekker (keeper)
( 1,  2, 'selected'),
( 1,  3, 'selected'),
( 1,  4, 'selected'),
( 1,  5, 'selected'),
( 1,  6, 'selected'),
( 1,  7, 'selected'),
( 1,  8, 'selected'),
( 1,  9, 'selected'),
( 1, 10, 'selected'),
( 1, 11, 'selected'),
( 1, 12, 'available'),   -- reserve
( 1, 13, 'available'),   -- reserve
( 1, 14, 'unavailable'), -- ziek
( 1, 15, 'unavailable'); -- afgemeld

-- Wedstrijd 2: FC Noordveld O15 vs SC Rivierstaete (gedeeltelijk ingevuld)
INSERT INTO match_players (match_id, player_id, status) VALUES
( 2,  1, 'selected'),
( 2,  2, 'selected'),
( 2,  3, 'selected'),
( 2,  4, 'available'),
( 2,  5, 'available'),
( 2,  6, 'unavailable'),
( 2,  7, 'unknown'),
( 2,  8, 'unknown');

-- Wedstrijd 3: FC Noordveld O15 vs FC Waterland (alleen opgave, nog geen selectie)
INSERT INTO match_players (match_id, player_id, status) VALUES
( 3,  1, 'available'),
( 3,  2, 'available'),
( 3,  3, 'available'),
( 3,  4, 'available'),
( 3,  5, 'available'),
( 3,  6, 'available'),
( 3,  7, 'available'),
( 3,  8, 'available'),
( 3,  9, 'available'),
( 3, 10, 'available'),
( 3, 11, 'available'),
( 3, 14, 'unavailable'),
( 3, 15, 'unknown');

-- Wedstrijden 4 t/m 12 (O15): geen aanwezigheid ingevuld — lege staat

-- Wedstrijd 14: FC Noordveld O17 vs Ajax (gedeeltelijk ingevuld)
INSERT INTO match_players (match_id, player_id, status) VALUES
(14, 17, 'selected'),
(14, 18, 'selected'),
(14, 19, 'selected'),
(14, 20, 'selected'),
(14, 21, 'selected'),
(14, 22, 'selected'),
(14, 23, 'selected'),
(14, 24, 'selected'),
(14, 25, 'selected'),
(14, 26, 'selected'),
(14, 27, 'selected'),
(14, 28, 'available'),
(14, 29, 'unavailable'),
(14, 30, 'unavailable'); -- inactive player, still has history

-- Wedstrijd 20: VV Ridderhoek A1 vs CSV Zandvoort (volledig ingevuld)
INSERT INTO match_players (match_id, player_id, status) VALUES
(20, 31, 'selected'),
(20, 32, 'selected'),
(20, 33, 'selected'),
(20, 34, 'selected'),
(20, 35, 'selected'),
(20, 36, 'selected'),
(20, 37, 'selected'),
(20, 38, 'selected'),
(20, 39, 'selected'),
(20, 40, 'selected'),
(20, 41, 'selected'),
(20, 42, 'available'),
(20, 43, 'unavailable');

-- Wedstrijd 27: SC Dalstein JO13 vs FC Dalstein (recent aangemaakt, deels ingevuld)
INSERT INTO match_players (match_id, player_id, status) VALUES
(27, 44, 'available'),
(27, 45, 'available'),
(27, 46, 'available'),
(27, 47, 'unknown'),
(27, 48, 'unknown'),
(27, 49, 'unavailable');

-- =============================================================
-- EINDE SEED
-- =============================================================
-- Samenvatting:
--   3 gebruikers
--   5 teams (4 actief, 1 gearchiveerd)
--   55 spelers verdeeld over 4 actieve teams
--   29 wedstrijden (mix van aankomend, gepland, gearchiveerd)
--   Aanwezigheid ingevuld voor wedstrijden 1, 2, 3, 14, 20, 27
--   Wedstrijd 13 is gearchiveerd (vorig seizoen)
-- =============================================================
