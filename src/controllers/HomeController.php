<?php

namespace Controller;

use Service\TodoService;

class HomeController {
    public function index() {
        $todoService = new TodoService();
        $vars = [ 'tasks' => $todoService->getAll() ];
        return view('home', layout: true, vars: $vars);
    }
}