<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/models/UserModel.php';

class AuthService
{
    private UserModel $users;

    // Dummy hash used to keep timing consistent when email is not found
    private const DUMMY_HASH = '$2y$12$invalidsaltinvalidsaltinvalidsa.invalidsaltinvalidsaltinv';

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);

        // Always run password_verify to prevent timing-based email enumeration
        $hash = $user['password_hash'] ?? self::DUMMY_HASH;
        if (!password_verify($password, $hash) || $user === null) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_activity'] = time();

        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        if (!isset($_SESSION['user_id'], $_SESSION['last_activity'])) {
            return false;
        }

        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS) {
            $this->logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }
}
