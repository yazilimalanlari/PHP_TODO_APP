<?php

namespace Controller;

use Kernel\Web\Request;
use Kernel\Web\Validator;
use Service\UserService;

class EntryController extends Validator {
    protected function format(): array {
        $username = 'required&minlength:3&maxlength:255';
        $password = 'required&minlength:6&maxlength:255';
        return [
            'register' => [
                'email' => 'required&type:email&maxlength:255',
                'username' => $username,
                'password' => $password
            ],
            'login' => [
                'username' => $username,
                'password' => $password
            ]
        ];
    }
    
    public function __construct() {
        $this->service = new UserService();
    }

    public function register(Request $req): string {
        $validation = $this->validate('register', $req->getInputs());
        
        if (array_key_exists('errors', $validation)) {
            return response($validation);
        }
        
        $data = [
            ...$validation['data'],
            'created' => date('Y-m-d H:i:s')
        ];

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $result = $this->service->createUser($data);

        return response([ 'status' => $result ?? 'failed' ]);
    }

    public function registerShow(): string {
        return view('register', [ 'title' => 'Sign Up' ], 'entry');
    }

    public function login(Request $req): string {
        $validation = $this->validate('login', $req->getInputs());
        
        if (array_key_exists('errors', $validation)) {
            return response($validation);
        }

        $user = $this->service->getUserForUsername($validation['data']['username']);

        if (!$user) {
            return response(array(
                'errors' => [ '$general' => 'User not found!' ]
            ));
        } else if (!password_verify($validation['data']['password'], $user->password)) {
            return response(array(
                'errors' => [ '$general' => 'Invalid password!' ]
            ));
        }

        $req->auth->setUser(array(
            'id' => $user->id, 
            'username' => $user->username 
        ));

        return response([ 'status' => 'success' ]);
    }

    public function loginShow(): string {
        return view('login', [ 'title' => 'Sign In' ], 'entry');
    }
}