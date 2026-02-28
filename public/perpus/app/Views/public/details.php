<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<div class="bg-white rounded-xl shadow p-8 max-w-3xl mx-auto">
    <div class="flex gap-8">
        <!-- Cover -->
        <div class="w-48 flex-shrink-0">
            <?php if ($book['cover']): ?>
                <img src="/uploads/covers/<?= $book['cover'] ?>" alt="<?= $book['title'] ?>" class="w-full rounded-lg shadow">
            <?php else: ?>
                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-5xl">📖</div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800"><?= $book['title'] ?></h1>
            <p class="text-gray-500 mt-1">✍️ <?= $book['author'] ?></p>
            <p class="text-gray-500 text-sm">🏷️ <?= $book['category'] ?></p>

            <span class="inline-block mt-3 text-sm px-3 py-1 rounded-full <?= $book['status'] === 'available' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                <?= $book['status'] === 'available' ? '✅ Tersedia' : '❌ Sedang Dipinjam' ?>
            </span>

            <p class="text-gray-600 mt-4 leading-relaxed"><?= $book['description'] ?></p>

            <?php if ($book['status'] === 'available'): ?>
            <a href="https://wa.me/628XXXXXXXXXX?text=Halo, saya ingin meminjam buku: <?= urlencode($book['title']) ?>"
               target="_blank"
               class="inline-block mt-6 bg-green-500 text-white px-6 py-3 rounded-full hover:bg-green-600 font-semibold">
                💬 Ajukan Pinjam via WhatsApp
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>