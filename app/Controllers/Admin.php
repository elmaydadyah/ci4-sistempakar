<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Admin extends BaseController
{
    public function indexDiagnosa()
    {
        return view('admin/diagnosa/index_diagnosa');
    }

    public function indexPenyakit()
    {
        return view('admin/penyakit/index_penyakit');
    }
}