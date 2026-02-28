<?php

namespace App\Controllers;

use App\Models\BookModel;

class Books extends BaseController
{
    public function index()
    {
        $model = new BookModel();
        $data = [
            'title' => 'Daftar Buku',
            'books' => $model->findAll(),
        ];
        return view('public/books', $data);
    }

    public function detail($id)
    {
        $model = new BookModel();
        $data = [
            'title' => 'Detail Buku',
            'book'  => $model->find($id),
        ];
        return view('public/detail', $data);
    }
}