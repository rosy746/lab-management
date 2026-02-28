<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Perpustakaan' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-blue-600">📚 Perpustakaanku</a>
            <div class="flex gap-6">
                <a href="/" class="text-gray-600 hover:text-blue-600">Beranda</a>
                <a href="/books" class="text-gray-600 hover:text-blue-600">Daftar Buku</a>
                <a href="/about" class="text-gray-600 hover:text-blue-600">Tentang</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto px-6 py-8">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner mt-10">
        <div class="container mx-auto px-6 py-4 text-center text-gray-500 text-sm">
            © <?= date('Y') ?> Perpustakaanku. All rights reserved.
        </div>
    </footer>

</body>
</html>