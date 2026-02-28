<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'title',
        'author', 
        'category',
        'description',
        'cover',
        'status'
    ];
    protected $useTimestamps    = true;

    // Ambil semua buku yang tersedia
    public function getAvailable()
    {
        return $this->where('status', 'available')->findAll();
    }

    // Ambil semua buku yang sedang dipinjam
    public function getBorrowed()
    {
        return $this->where('status', 'borrowed')->findAll();
    }
}