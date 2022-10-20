<?php

namespace Kernel\Database;

use Kernel\Database;

class Model extends Database {
    public function __construct() {
        parent::__construct(getenv('DB_NAME'), $this->table);
    }

    public function setup() {
        parent::createConnection(true);
        parent::createTable($this->table, $this->fields);
    }

    public function create(array $data): int {
        return parent::insert($data);
    }

    public function find(array|string $fields = []): array {
        return parent::select($fields);
    }

    public function sort(string $field, int $order): Database|Model {
        return parent::orderBy($field, $order);
    }
}