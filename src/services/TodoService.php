<?php

namespace Service;

use Exception;
use \Model\TodoModel;

class TodoService {
    private TodoModel $model;

    public function __construct() {
        $this->model = new TodoModel();
    }

    public function addTask(array $data): int|bool {
        try {
            return $this->model->create($data);            
        } catch (Exception $e) {
            return false;
        }
    }

    public function getTasks(int $owner, int $skip, int $limit): array {
        $result = $this->model
            ->where('owner', $owner)
            ->skip($skip)
            ->limit($limit)
            ->find();
        
        return [
            'totalCount' => $result['totalCount'],
            'tasks' => $result['data']
        ];
    }

    public function setComplete(int $owner, int $id, int $value): bool {
        try {
            return $this->model
                ->where('owner', $owner)
                ->where('id', $id)
                ->update([ 'completed' => $value ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateTask(int $owner, int $id, string $content): bool {
        try {
            return $this->model
                ->where('owner', $owner)
                ->where('id', $id)
                ->update([ 'content' => $content, 'updated' => date('Y-m-d H:i:s') ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteTask(int $owner, int $id): bool {
        try {
            return $this->model
                ->where('owner', $owner)
                ->where('id', $id)
                ->delete();
        } catch (Exception $e) {
            return false;
        }
    }
}