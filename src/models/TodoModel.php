<?php

namespace Model;

use Kernel\Database\Model;

class TodoModel extends Model {
    protected string $table = 'tasks';
    protected array $fields = [
        'content' => [
            'type' => 'string',
            'required' => true
        ],
        'owner' => [
            'type' => 'int',
            'required' => true
        ],
        'completed' => [
            'type' => 'int'
        ],
        'updated' => [
            'type' => 'date'
        ],
        'created' => [
            'type' => 'date',
            'required' => true
        ]
    ];
}

// $model = new TodoModel();
// $model->create([
//     'content' => 'test content',
//     'created' => time()
// ]);

// $model->where('id', 1)->delete();

// $tasks = $model
//     ->sort('created', -1)
//     ->skip(10)
//     ->limit(10)
//     ->find('content created');
// print_r($tasks);

// $model->where('id', 10)->update(['content' => 'test content 10', 'created' => time()]);