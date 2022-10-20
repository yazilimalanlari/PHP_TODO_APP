<?php

namespace Service;

use \Model\TodoModel;

class TodoService {
    private TodoModel $model;

    public function __construct() {
        $this->model = new TodoModel();
    }

    public function getAll() {        
        return $this->model->find();
    }
}