<?php

namespace App\Models;

use InvalidArgumentException;
use PDO;

class User extends BaseModels {

    protected string $table = 'users';


    private ?int $user_id = null;
    private string $username;
    private string $email;
    private string $password;
    private string $role = 'user';
    
    // Getters
    public function getId(): ?int {
        return $this->user_id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getRole(): string {
        return $this->role;
    }
    
    
    
    // Setters avec validation 
    public function setUsername(string $username): self {
        return $this;
    }

    public function setEmail(string $email): self {
        return $this;
    }

    public function setPassword(string $password): self {
        return $this;
    }

    public function setRole(string $role): self {
        return $this;
    }
}


