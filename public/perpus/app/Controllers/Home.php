<?php

namespace App\Controllers;

use App\Models\BookModel;

class Home extends BaseController
{
    public function index()
    {
        $model = new BookModel();
        $data = [
            'title'     => 'Beranda',
            'total'     => $model->countAll(),
            'available' => $model->where('status', 'available')->countAllResults(),
            'borrowed'  => $model->where('status', 'borrowed')->countAllResults(),
            'latest'    => $model->orderBy('created_at', 'DESC')->limit(4)->find(),
        ];
        return view('public/home', $data);
    }
}