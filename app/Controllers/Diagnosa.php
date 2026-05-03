<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Diagnosa extends BaseController
{
    public function index()
    {
        return view('diagnosa/index');
    }
}