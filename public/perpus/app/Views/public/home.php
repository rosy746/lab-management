<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<div class="bg-blue-600 text-white rounded-2xl px-10 py-14 mb-10 text-center">
    <h1 class="text-4xl font-bold mb-3">Selamat Datang di Perpustakaan IKANNN 📚</h1>
    <p class="text-lg text-blue-100 mb-6">Temukan koleksi buku favoritmu di sini</p>
    <a href="/books" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-full hover:bg-blue-50">
        Lihat Koleksi Buku
    </a>
</div>

<!-- Statistik -->
<div class="grid grid-cols-3 gap-6 mb-10">
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-4xl font-bold text-blue-600"><?= $total ?></p>
        <p class="text-gray-500 mt-1">Total Buku</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-4xl font-bold text-green-500"><?= $available ?></p>
        <p class="text-gray-500 mt-1">Tersedia</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-4xl font-bold text-red-500"><?= $borrowed ?></p>
        <p class="text-gray-500 mt-1">Sedang Dipinjam</p>
    </div>
</div>

<!-- Buku Terbaru -->
<h2 class="text-2xl font-bold text-gray-700 mb-6">Buku Terbaru</h2>
<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <?php foreach ($latest as $book): ?>
    <a href="/books/<?= $book['id'] ?>" class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
        <?php if ($book['cover']): ?>
            <img src="/uploads/covers/<?= $book['cover'] ?>" alt="<?= $book['title'] ?>" class="w-full h-48 object-cover">
        <?php else: ?>
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400 text-4xl">📖</div>
        <?php endif; ?>
        <div class="p-4">
            <h3 class="font-semibold text-gray-700 text-sm truncate"><?= $book['title'] ?></h3>
            <p class="text-gray-400 text-xs mt-1"><?= $book['author'] ?></p>
            <span class="inline-block mt-2 text-xs px-2 py-1 rounded-full <?= $book['status'] === 'available' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                <?= $book['status'] === 'available' ? 'Tersedia' : 'Dipinjam' ?>
            </span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>