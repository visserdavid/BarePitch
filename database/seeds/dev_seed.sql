INSERT INTO users (email, password_hash, display_name) VALUES (
    'coach@example.test',
    '$2y$12$EyTLwTXnRHP2MKTPjmeHHuPOl345P0Xu1ipiObsslW0ItUXuI7eze',
    'Test Coach'
);

INSERT INTO teams (user_id, name, season, status) VALUES
(1, 'FC Testclub', '2025-2026', 'active');

INSERT INTO players (team_id, display_name, shirt_number, status) VALUES
(1, 'Jan de Vries', 1, 'active'),
(1, 'Pieter Bakker', 5, 'active'),
(1, 'Klaas Smit', 9, 'active'),
(1, 'Thomas Visser', 11, 'active'),
(1, 'Erik Meijer', NULL, 'active');
