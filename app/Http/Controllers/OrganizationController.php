<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with(['classes' => function($q) {
            $q->orderBy('grade_level')->orderBy('name');
        }])->orderBy('name')->get();
        return view('organizations.index', compact('organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        Organization::create($validated);

        return back()->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $organization->update($validated);

        return back()->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        return back()->with('success', 'Sekolah berhasil dihapus.');
    }
}
