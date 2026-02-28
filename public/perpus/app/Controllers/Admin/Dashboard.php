<?php

namespace App\Controllers\Admin;

use App\Models\BookModel;

class Dashboard extends \App\Controllers\BaseController
{
    public function index()
    {
        $model = new BookModel();
        $data = [
            'title'     => 'Dashboard',
            'total'     => $model->countAll(),
            'available' => $model->where('status', 'available')->countAllResults(),
            'borrowed'  => $model->where('status', 'borrowed')->countAllResults(),
        ];
        return view('admin/dashboard', $data);
    }
}