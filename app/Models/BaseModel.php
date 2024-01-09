<?php

namespace App\Models;

use App\Services\PDOWrapper;
use PDO;

class BaseModel {

    protected $pdoWrapper;
    protected $tableName;

    public function __construct(PDOWrapper $pdoWrapper, $tableName) {
        $this->pdoWrapper = $pdoWrapper;
        $this->tableName = $tableName;
    }

    public function getAll() {
        $query = "SELECT * FROM $this->tableName";
        $stmt = $this->pdoWrapper->executeStatement($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM $this->tableName WHERE id = :id";
        $params = [':id' => $id];

        $stmt = $this->pdoWrapper->executeStatement($query, $params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_map(function ($value) {
            return ":$value";
        }, array_keys($data)));

        $query = "INSERT INTO $this->tableName ($columns) VALUES ($values)";

        $this->pdoWrapper->executeStatement($query, $data);
    }

    public function update($id, $data) {
        $setClause = implode(', ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($data)));

        $query = "UPDATE $this->tableName SET $setClause WHERE id = :id";
        $data[':id'] = $id;

        $this->pdoWrapper->executeStatement($query, $data);
    }

    public function delete($id) {
        $query = "DELETE FROM $this->tableName WHERE id = :id";
        $params = [':id' => $id];

        $this->pdoWrapper->executeStatement($query, $params);
    }

    public function isFieldUnique($table, $field, $value, $excludingId = null, $excludingField = 'id') {
        $query = "SELECT COUNT(*) FROM {$table} WHERE {$field} = :value";

        // If updating, exclude the current value from the check
        if ($excludingId !== null) {
            $query .= " AND {$excludingField} != :excludingId";
        }

        $params = [
            ':value' => $value,
        ];

        if ($excludingId !== null) {
            $params[':excludingId'] = $excludingId;
        }

        $stmt = $this->pdoWrapper->executeStatement($query, $params);

        $count = $stmt->fetchColumn();

        return ($count === 0);
    }
}