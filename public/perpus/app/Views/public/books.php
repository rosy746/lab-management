<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<h2 class="text-2xl font-bold text-gray-700 mb-6">Daftar Buku</h2>

<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <?php foreach ($books as $book): ?>
    <a href="/books/<?= $book['id'] ?>" class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
        <?php if ($book['cover']): ?>
            <img src="/uploads/covers/<?= $book['cover'] ?>" alt="<?= $book['title'] ?>" class="w-full h-48 object-cover">
        <?php else: ?>
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400 text-4xl">📖</div>
        <?php endif; ?>
        <div class="p-4">
            <h3 class="font-semibold text-gray-700 text-sm truncate"><?= $book['title'] ?></h3>
            <p class="text-gray-400 text-xs mt-1"><?= $book['author'] ?></p>
            <p class="text-gray-400 text-xs"><?= $book['category'] ?></p>
            <span class="inline-block mt-2 text-xs px-2 py-1 rounded-full <?= $book['status'] === 'available' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                <?= $book['status'] === 'available' ? 'Tersedia' : 'Dipinjam' ?>
            </span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>