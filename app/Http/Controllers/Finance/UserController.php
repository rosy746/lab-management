<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\FinanceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = FinanceUser::orderBy('role')->orderBy('name')->get();
        return view('finance.users.index', compact('users'));
    }

    public function create()
    {
        return view('finance.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:finance.finance_users,email',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'     => 'required|in:admin,bendahara',
            'phone'    => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        FinanceUser::create($validated);

        return redirect()
            ->route('finance.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(FinanceUser $user)
    {
        return view('finance.users.edit', compact('user'));
    }

    public function update(Request $request, FinanceUser $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:finance.finance_users,email,' . $user->id,
            'password'  => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'role'      => 'required|in:admin,bendahara',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Admin tidak bisa menonaktifkan dirinya sendiri
        if ($user->id === auth('finance')->id() && isset($validated['is_active']) && !$validated['is_active']) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update($validated);

        return redirect()
            ->route('finance.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(FinanceUser $user)
    {
        if ($user->id === auth('finance')->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
