<?php

namespace App\Controllers\Admin;

use App\Models\BookModel;

class Books extends \App\Controllers\BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new BookModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Buku',
            'books' => $this->model->findAll(),
        ];
        return view('admin/books/index', $data);
    }

    public function create()
    {
        $data = ['title' => 'Tambah Buku'];
        return view('admin/books/create', $data);
    }

    public function store()
    {
        $cover = $this->request->getFile('cover');
        $coverName = null;

        if ($cover && $cover->isValid()) {
            $coverName = $cover->getRandomName();
            $cover->move(ROOTPATH . 'public/uploads/covers', $coverName);
        }

        $this->model->insert([
            'title'       => $this->request->getPost('title'),
            'author'      => $this->request->getPost('author'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'cover'       => $coverName,
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/books');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Buku',
            'book'  => $this->model->find($id),
        ];
        return view('admin/books/edit', $data);
    }

    public function update($id)
    {
        $cover = $this->request->getFile('cover');
        $book  = $this->model->find($id);
        $coverName = $book['cover'];

        if ($cover && $cover->isValid()) {
            // Hapus cover lama
            if ($coverName && file_exists(ROOTPATH . 'public/uploads/covers/' . $coverName)) {
                unlink(ROOTPATH . 'public/uploads/covers/' . $coverName);
            }
            $coverName = $cover->getRandomName();
            $cover->move(ROOTPATH . 'public/uploads/covers', $coverName);
        }

        $this->model->update($id, [
            'title'       => $this->request->getPost('title'),
            'author'      => $this->request->getPost('author'),
            'category'    => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'cover'       => $coverName,
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/books');
    }

    public function delete($id)
    {
        $book = $this->model->find($id);

        if ($book['cover'] && file_exists(ROOTPATH . 'public/uploads/covers/' . $book['cover'])) {
            unlink(ROOTPATH . 'public/uploads/covers/' . $book['cover']);
        }

        $this->model->delete($id);
        return redirect()->to('/admin/books');
    }
}