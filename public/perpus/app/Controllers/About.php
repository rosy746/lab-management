<?php

namespace App\Controllers;

class About extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Tentang',
        ];
        return view('public/about', $data);
    }
}