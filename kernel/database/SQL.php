<?php

namespace Kernel\Database;

class SQL {
    protected $types = [
        'int' => [
            'mysql' => 'INT',
            'sqlite' => 'INTEGER'
        ],
        'string' => [
            'mysql' => 'TEXT',
            'sqlite' => 'TEXT'
        ],
        'date' => [
            'mysql' => 'TIMESTAMP',
            'sqlite' => 'TEXT'
        ]
    ];
    
    public function __construct(private string $db) {}
    
    protected function createTableGenerateSQL(string $tableName, array $fields): string {
        switch ($this->db) {
            case 'MySQL': return $this->createTableGenerateSQLForMysql($tableName, $fields);
            case 'SQLite3': return $this->createTableGenerateSQLForSqlite($tableName, $fields);
        }
    }

    private function createTableGenerateSQLForMysql(string $tableName, array $fields): string {
        $sql = <<<SQL
        CREATE TABLE $tableName (
            id INT NOT NULL AUTO_INCREMENT UNIQUE,
            
        SQL;

        foreach ($fields as $field => $value) {
            $type = $this->types[$value['type']]['mysql'];

            if (array_key_exists('maxlength', $value) && $type === 'TEXT') 
                $type = "VARCHAR({$value['maxlength']})";

            $sql .= $field . ' ' . $type;
            
            if (array_key_exists('required', $value) && $value['required'] === true) {
                $sql .= ' NOT NULL';
            }

            if (array_key_exists('unique', $value) && $value['unique'] === true) {
                $sql .= ' UNIQUE';
            }

            $sql .= ",";
        }
        return $sql . ' PRIMARY KEY (id))';
    }

    private function createTableGenerateSQLForSqlite(string $tableName, array $fields): string {
        $sql = <<<SQL
        CREATE TABLE $tableName (
            id INTEGER,
            
        SQL;

        foreach ($fields as $field => $value) {
            $sql .= $field . ' ' . $this->types[$value['type']]['sqlite'];
            
            if (array_key_exists('required', $value) && $value['required'] === true) {
                $sql .= ' NOT NULL';
            }

            if (array_key_exists('unique', $value) && $value['unique'] === true) {
                $sql .= ' UNIQUE';
            }

            $sql .= ",";
        }
        return $sql . ' PRIMARY KEY (id AUTOINCREMENT))';
    }

    protected function insertGenerateSQL(string $tableName, array $data): string {
        $keys = join(',', array_keys($data));
        $values = rtrim(str_repeat('?,', count($data)), ',');
        $sql = "INSERT INTO $tableName ($keys) VALUES ($values)";
        return $sql;
    }

    protected function deleteGenerateSQL(string $tableName, array $whereStatements): string {
        $where = $this->whereStatementsGenerateSQL($whereStatements);
        return "DELETE FROM $tableName WHERE $where";
    }

    protected function selectGenerateSQL(
        string $tableName, 
        array|string $fields, 
        array $whereStatements, 
        array $orderBy,
        int $skip,
        ?int $limit
    ): string {
        $fields = empty($fields) ? '*' : (is_array($fields) ? join(',', $fields) : implode(',', explode(' ', $fields)));
        $sql = "SELECT $fields FROM $tableName";

        if (!empty($whereStatements)) {
            $sql .= ' WHERE ' . $this->whereStatementsGenerateSQL($whereStatements);
        }

        if (!empty($orderBy)) {
            $sql .= sprintf(' ORDER BY %s %s', $orderBy['field'], $orderBy['order'] === 1 ? 'ASC' : 'DESC');
        }

        if ($limit != null) $sql .= " LIMIT $skip, $limit";

        return $sql;
    }
    
    protected function updateGenerateSQL(string $tableName, array $data, array $whereStatements): string {
        $fields = join('=?, ', array_keys($data)) . '=?';
        $where = $this->whereStatementsGenerateSQL($whereStatements);
        $sql = "UPDATE $tableName SET $fields WHERE $where";
        return $sql;
    }

    private function whereStatementsGenerateSQL(array $whereStatements): string {
        $sql = '';
        foreach ($whereStatements as $item) {
            if (!empty($sql)) $sql .= $item['logicalOperator'];
            $sql .= sprintf(' %s%s? ', $item['field'], $item['comparisonOperator']);
        }

        return trim($sql);
    }
}