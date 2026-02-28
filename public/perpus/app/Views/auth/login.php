<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-lg p-10 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">📚</div>
            <h1 class="text-2xl font-bold text-gray-800">Login Admin</h1>
            <p class="text-gray-400 text-sm mt-1">Perpustakaan IKANNN</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Username</label>
                <input type="text" name="username" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-semibold">
                Login
            </button>
        </form>
    </div>

</body>
</html>