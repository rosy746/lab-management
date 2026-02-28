<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-700">Daftar Buku</h2>
    <a href="/admin/books/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Tambah Buku</a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-6 py-3 text-left">Cover</th>
                <th class="px-6 py-3 text-left">Judul</th>
                <th class="px-6 py-3 text-left">Pengarang</th>
                <th class="px-6 py-3 text-left">Kategori</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($books as $book): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-3">
                    <?php if ($book['cover']): ?>
                        <img src="/uploads/covers/<?= $book['cover'] ?>" class="w-12 h-16 object-cover rounded">
                    <?php else: ?>
                        <div class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center text-xl">📖</div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-3 font-medium text-gray-700"><?= $book['title'] ?></td>
                <td class="px-6 py-3 text-gray-500"><?= $book['author'] ?></td>
                <td class="px-6 py-3 text-gray-500"><?= $book['category'] ?></td>
                <td class="px-6 py-3">
                    <span class="px-2 py-1 rounded-full text-xs <?= $book['status'] === 'available' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                        <?= $book['status'] === 'available' ? 'Tersedia' : 'Dipinjam' ?>
                    </span>
                </td>
                <td class="px-6 py-3 flex gap-2 items-center">
                    <a href="/admin/books/edit/<?= $book['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-xs">Edit</a>
                    <a href="/admin/books/delete/<?= $book['id'] ?>" 
                       onclick="return confirm('Yakin hapus buku ini?')"
                       class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>