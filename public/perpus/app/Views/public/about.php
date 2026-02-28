<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<div class="bg-white rounded-xl shadow p-10 max-w-2xl mx-auto text-center">
    <div class="text-6xl mb-4">📚</div>
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Tentang Perpustakaan IKANNN</h1>
    <p class="text-gray-600 leading-relaxed">
        Perpustakaanku adalah koleksi buku pribadi yang dibagikan untuk umum. 
        Kamu bisa melihat buku apa saja yang tersedia dan mengajukan peminjaman 
        langsung via WhatsApp.
    </p>
    <p class="text-gray-500 mt-4 text-sm">Dibuat dengan ❤️ menggunakan CodeIgniter 4 & Tailwind CSS</p>
</div>

<?= $this->endSection() ?>
```

---

Jangan lupa ganti nomor WA di `detail.php`:
```
https://wa.me/628XXXXXXXXXX