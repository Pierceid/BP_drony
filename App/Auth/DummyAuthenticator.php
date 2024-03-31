<?php

namespace App\Auth;

use App\Core\IAuthenticator;
use App\Models\User;

/**
 * Class DummyAuthenticator
 * Basic implementation of user authentication
 * @package App\Auth
 */
class DummyAuthenticator implements IAuthenticator
{
    public const LOGIN = "admin";
    public const PASSWORD_HASH = '$2y$10$GRA8D27bvZZw8b85CAwRee9NH5nj4CQA6PDFMc90pN9Wi4VAWq3yq'; // admin
    public const USERNAME = "Admin";

    /**
     * DummyAuthenticator constructor
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Verify, if the user is already in DB
     * @param $login
     * @param $email
     * @return bool
     * @throws \Exception
     */
    public function register($login, $email): bool
    {
        $users = User::getAll();
        foreach ($users as $user) {
            if ($user->getEmail() == $email || $user->getLogin() == $login) {
                return false;
            }
        }
        $_SESSION['user'] = $login;
        return true;
    }

    /**
     * @param $email
     * @param $password
     * @return bool
     * @throws \Exception
     */
    public function login($email, $password): bool
    {
        $users = User::getAll();
        foreach ($users as $user) {
            if ($user->getEmail() == $email && password_verify($password, $user->getPassword())) {
                $_SESSION['user'] = $user->getLogin();
                return true;
            }
        }
        return false;
    }

    /**
     * Logout the user
     */
    public function logout(): void
    {
        if (isset($_SESSION["user"])) {
            unset($_SESSION["user"]);
            session_destroy();
        }
    }

    /**
     * Get the name of the logged-in user
     * @return string
     * @throws \Exception
     */
    public function getLoggedUserName(): string
    {
        return isset($_SESSION['user']) ? $_SESSION['user'] : throw new \Exception("User not logged in");
    }

    /**
     * Get the context of the logged-in user
     * @return string
     */
    public function getLoggedUserContext(): mixed
    {
        return null;
    }

    /**
     * Return if the user is authenticated or not
     * @return bool
     */
    public function isLogged(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user'] != null;
    }

    /**
     * Return the id of the logged-in user
     * @return mixed
     */
    public function getLoggedUserId(): mixed
    {
        return $_SESSION['user'];
    }
}
