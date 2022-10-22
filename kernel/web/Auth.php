<?php

namespace Kernel\Web;

class Auth {
    public bool $isAuth;
    
    public function __construct() {
        if (session_status() === 1) session_start();
        $this->isAuth = array_key_exists('user', $_SESSION) && is_object($_SESSION['user']);
    }

    public function setUser(array|object $user): void {
        $_SESSION['user'] = (object) $user;
    }

    public function getUser(): ?object {
        return $_SESSION['user'] ?? null;
    }

    public function getUserId(): ?int {
        return $this->getUser()?->id ?? null;        
    }

    public function removeUser(): void {
        session_destroy();
    }
}