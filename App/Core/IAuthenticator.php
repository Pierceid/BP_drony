<?php

namespace App\Core;

/**
 * Interface IAuthenticator
 * Interface for authentication
 * @package App\Core
 */
interface IAuthenticator
{
    /**
     * Perform user registration
     * @param $login
     * @param $email
     * @return bool
     */
    public function register($login, $email): bool;

    /**
     * Perform user login
     * @param $email
     * @param $password
     * @return bool
     */
    public function login($email, $password): bool;

    /**
     * Perform user login
     * @return void
     */
    public function logout(): void;

    /**
     * Return name of a logged user
     * @return string
     */
    public function getLoggedUserName(): string;

    /**
     * Return id of a logged user
     * @return mixed
     */
    public function getLoggedUserId(): mixed;

    /**
     * Return a context of logged user, e.g. user class instance
     * @return mixed
     */
    public function getLoggedUserContext(): mixed;

    /**
     * Return, if a user is logged or not
     * @return bool
     */
    public function isLogged(): bool;
}
