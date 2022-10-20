<?php

namespace Controller;

use Kernel\Web\Request;

class EntryController {
    public static function register(Request $req) {
        echo $req->queryClean('status');
        return response([
            'status' => 'success'
        ]);
    }
}