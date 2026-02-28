<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - Perpustakaanku</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-700 text-white flex flex-col">
            <div class="px-6 py-5 text-xl font-bold border-b border-blue-600">
                📚 Admin Panel
            </div>
            <nav class="flex flex-col gap-1 mt-4 px-4">
                <a href="/admin" class="px-4 py-2 rounded hover:bg-blue-600">Dashboard</a>
                <a href="/admin/books" class="px-4 py-2 rounded hover:bg-blue-600">Kelola Buku</a>
                <a href="/" class="px-4 py-2 rounded hover:bg-blue-600 mt-auto">← Ke Beranda</a>
            </nav>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow px-8 py-4">
                <h1 class="text-xl font-semibold text-gray-700"><?= $title ?? 'Admin' ?></h1>
            </header>
            <main class="p-8">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

</body>
</html>