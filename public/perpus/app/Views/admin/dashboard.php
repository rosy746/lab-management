<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="grid grid-cols-3 gap-6">
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

<div class="mt-8">
    <a href="/admin/books" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
        Kelola Buku →
    </a>
</div>

<?= $this->endSection() ?>