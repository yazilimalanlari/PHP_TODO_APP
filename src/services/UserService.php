<?php

namespace Service;

use Model\UserModel;

use Exception;
use stdClass;

class UserService {
    private UserModel $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    public function createUser(array $data): bool {
        try {
            $this->model->create($data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUserForUsername(string $username): stdClass|bool {
        try {
            return $this->model->where('username', $username)->findOne();
        } catch (Exception $e) {
            return false;
        }
    }
}