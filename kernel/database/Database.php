<?php

namespace Kernel;

use PDOException;
use Kernel\Database\SQL;
use SQLite3;
use PDO;
use Error;
use Kernel\Database\Model;

class Database extends SQL {
    private PDO|Sqlite3 $db;
    private array $whereStatements = [];
    private array $orderBy = [];
    private int $skip = 0;
    private ?int $limit = null;
    
    public function __construct(private string $dbName, private ?string $tableName = null) {
        parent::__construct(getenv('DB_SELECT'));
        
        if (!defined('BUILD')) $this->createConnection();
    }

    public function createTable(string $tableName, array $fields) {
        $sql = parent::createTableGenerateSQL($tableName, $fields);
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function createConnection(bool $isSetup = false): void {
        switch (getenv('DB_DRIVER')) {
            case 'PDO':
                try {
                    switch (getenv('DB_SELECT')) {
                        case 'MySQL':
                            $this->db = new PDO('mysql:host=' . getenv('MYSQL_HOST') . ";dbname=$this->dbName;charset=utf8", getenv('DB_USER'), getenv('DB_PASS'));
                            break;
                        case 'SQLite3':
                            $sqlitePath = join('/', [str_replace('$HOME', getenv('HOME'), getenv('SQLITE_PATH')), getenv('DB_NAME') . '.db']);
                            $this->db = new PDO("sqlite:$sqlitePath");
                            break;
                        }
                } catch (PDOException $e) {
                    if ($e->getCode() === 1049 && $isSetup && getenv('DB_SELECT') === 'MySQL') {
                        $this->db = new PDO('mysql:host=' . getenv('MYSQL_HOST'), getenv('DB_USER'), getenv('DB_PASS'));
                        $this->db->exec("set names utf8; CREATE DATABASE $this->dbName; use $this->dbName");
                    } else {
                        die($e->getMessage());
                    }
                }
                break;
            case 'SQLite3':
                break;
        }
    }

    protected function insert(array $data): int {
        $sql = parent::insertGenerateSQL($this->tableName, $data);
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_values($data));

        if ($result !== true) throw new Error('There is a problem!');
        return intval($this->db->lastInsertId());
    }

    public function delete(): bool {
        $sql = parent::deleteGenerateSQL($this->tableName, $this->whereStatements);
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($this->getWhereValues());
        $this->release();
        return boolval($result);
    }

    protected function select(array|string $fields = [], bool $multiple = true): array|object|bool {
        $sql = parent::selectGenerateSQL($this->tableName, $fields, $this->whereStatements, $this->orderBy, $this->skip, $this->limit);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->getWhereValues());

        if ($multiple) {
            $this->skip = 0;
            $this->limit = null;
            $this->release();
            return [
                'totalCount' => $this->count(),
                'data' => $stmt->fetchAll(PDO::FETCH_OBJ)
            ];
        }
        
        $this->release();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function count(): int {
        $sql = parent::selectCountGenerateSQL($this->tableName, $this->whereStatements, $this->orderBy);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->getWhereValues());
        return $stmt->fetchColumn();
    }

    public function findOne(array|string $fields = []): object|bool {
        return $this->select($fields, false);
    }

    public function update(array $data): bool {
        $sql = parent::updateGenerateSQL($this->tableName, $data, $this->whereStatements);
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_merge(array_values($data), $this->getWhereValues()));
        $this->release();
        return $result;
    }

    private function release(): void {
        $this->whereStatements = [];
        $this->orderBy = [];
        $this->skip = 0;
        $this->limit = null;
    }

    private function getWhereValues(): array {
        return array_column($this->whereStatements, 'value');
    }

    private function addWhereStatement(string $field, string $value, string $logicalOperator, string $comparisonOperator): void {
        $statement = [
            'field' => $field,
            'value' => $value,
            'logicalOperator' => $logicalOperator,
            'comparisonOperator' => $comparisonOperator
        ];
        array_push($this->whereStatements, $statement);
    }

    public function where(string $field, mixed $value): Database|Model {
        $this->addWhereStatement($field, $value, 'AND', '=');
        return $this;
    }

    public function whereNotEqual(string $field, mixed $value): Database|Model {
        $this->addWhereStatement($field, $value, 'AND', '!=');
        return $this;
    }

    public function whereOrEqual(string $field, mixed $value): Database|Model {
        $this->addWhereStatement($field, $value, 'OR', '=');
        return $this;
    }

    public function whereOrNotEqual(string $field, mixed $value): Database|Model {
        $this->addWhereStatement($field, $value, 'OR', '!=');
        return $this;
    }

    protected function orderBy(string $field, int $order): Database|Model {
        $this->orderBy = [ 'field' => $field, 'order' => $order ];
        return $this;
    }

    public function skip(int $value): Database|Model {
        $this->skip = $value;
        return $this;
    }

    public function limit(int $value): Database|Model {
        $this->limit = $value;
        return $this;
    }
}