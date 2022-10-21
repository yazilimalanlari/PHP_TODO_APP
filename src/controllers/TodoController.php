<?php

namespace Controller;

use Kernel\Web\Request;
use Kernel\Web\Validator;
use Service\TodoService;

class TodoController extends Validator {
    private TodoService $service;
    const PER_PAGE = 10;
    
    protected function format(): array {
        $content = 'required&minlength:1';
        return [
            'create' => [ 'content' => $content ],
            'update' => [ 'content' => $content ],
            'complete' => [ 'status' => 'required&type:int' ]
        ];
    }
    
    public function __construct() {
        $this->service = new TodoService();
    }
    
    public function home(Request $req): string {
        $user = $req->auth->getUser();

        if ($user == null) {
            header('Location: /login');
            exit;
        }

        $page = is_numeric($req->queryFilter('page')) ? $req->queryFilter('page') : 1;
        $skip = ($page - 1) * self::PER_PAGE;
        $limit = self::PER_PAGE;

        $result = $this->service->getTasks($user->id, $skip, $limit);

        $vars = array(
            'totalPage' => ceil($result['totalCount'] / self::PER_PAGE),
            'tasks' => $result['tasks']
        );

        return view('home', layout: true, vars: $vars);
    }

    public function create(Request $req): string {
        $validation = $this->validate('create', $req->getInputs());
        if (array_key_exists('errors', $validation)) return response($validation);

        $user = $req->auth->getUser();

        $data = [
            ...$validation['data'],
            'owner' => $user?->id,
            'created' => date('Y-m-d H:i:s')
        ];

        $result = $this->service->addTask($data);
        if ($result === false) return response(array( 
            'errors' => [ 
                '$general' => 'There was a problem adding the task!' 
            ]
        ));

        return response(array( 'data' => [ 'id' => $result ] ));
    }

    public function update(Request $req): string {
        $validation = $this->validate('update', $req->getInputs());
        if (array_key_exists('errors', $validation)) return response($validation);

        $result = $this->service->updateTask($req->auth->getUserId(), $req->param('id'), $req->inputFilter('content'));
        
        if ($result === false) return response(array( 
            'errors' => [ 
                '$general' => 'There was a problem updating the task!' 
            ]
        ));
        
        return response(array( 'status' => 'success' ));
    }
    
    public function delete(Request $req): string {
        $result = $this->service->deleteTask($req->auth->getUserId(), $req->param('id'));
        if (!$result) return response(array( 'errors' => [ '$general' => 'There was a problem updating the task!' ] ));
        return response(array( 'status' => 'success' ));
    }

    public function setComplete(Request $req): string {
        $validation = $this->validate('complete', $req->getInputs());
        if (array_key_exists('errors', $validation)) return response($validation);

        $user = $req->auth->getUser();
        $updated = $this->service->setComplete($user?->id, $req->param('id'), $validation['data']['status'] == 1 ? 1 : 0);

        if (!$updated)
            return response(array( 'errors' => [ '$general' => 'There is a problem!' ] ));
        return response([ 'status' => 'success' ]);
    }
}