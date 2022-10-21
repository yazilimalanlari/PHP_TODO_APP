<?php

namespace Model;

use Kernel\Database\Model;

class UserModel extends Model {
    protected string $table = 'users';
    protected array $fields = [
        'email' => [
            'type' => 'string',
            'required' => true,
            'unique' => true,
            'maxlength' => 255
        ],
        'username' => [
            'type' => 'string',
            'required' => true,
            'unique' => true,
            'maxlength' => 255
        ],
        'password' => [
            'type' => 'string',
            'required' => true
        ],
        'created' => [
            'type' => 'date',
            'required' => true
        ]
    ];
}