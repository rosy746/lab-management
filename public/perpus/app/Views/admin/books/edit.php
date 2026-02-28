<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <h2 class="text-xl font-bold text-gray-700 mb-6">Edit Buku</h2>

    <form action="/admin/books/update/<?= $book['id'] ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Judul</label>
            <input type="text" name="title" value="<?= $book['title'] ?>" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Pengarang</label>
            <input type="text" name="author" value="<?= $book['author'] ?>" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
            <input type="text" name="category" value="<?= $book['category'] ?>" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Deskripsi</label>
            <textarea name="description" rows="4" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"><?= $book['description'] ?></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600 mb-1">Cover Buku</label>
            <?php if ($book['cover']): ?>
                <img src="/uploads/covers/<?= $book['cover'] ?>" class="w-24 h-32 object-cover rounded mb-2">
            <?php endif; ?>
            <input type="file" name="cover" accept="image/*" class="w-full border rounded-lg px-4 py-2">
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti cover</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="available" <?= $book['status'] === 'available' ? 'selected' : '' ?>>Tersedia</option>
                <option value="borrowed" <?= $book['status'] === 'borrowed' ? 'selected' : '' ?>>Dipinjam</option>
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold">Update</button>
            <a href="/admin/books" class="bg-gray-200 text-gray-600 px-6 py-2 rounded-lg hover:bg-gray-300">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>