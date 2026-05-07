<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('layout/landing/header')
            . view('layout/landing/navbar')
            . view('layout/landing/content')
            . view('layout/landing/footer');
    }
}
