<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected ?int $id = null;
    protected ?string $login;
    protected ?string $email;
    protected ?string $password;
    protected ?int $isAdmin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getIsAdmin(): ?int
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(?int $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }
}
