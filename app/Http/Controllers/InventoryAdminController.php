<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabInventory;
use App\Models\Resource;

class InventoryAdminController extends Controller
{
    private function getAllowedResources(): ?array
    {
        $user = auth()->user();
        if (in_array($user->role, ['admin', 'operator'])) return null;
        $meta = is_array($user->metadata)
            ? $user->metadata
            : json_decode($user->metadata, true);
        return $meta['allowed_resources'] ?? [];
    }

    private function getAllowedResourceIds(): array
    {
        $allowed = $this->getAllowedResources();
        if ($allowed !== null) return $allowed;
        return Resource::where('status', 'active')->pluck('id')->toArray();
    }

    public function index(Request $request)
    {
        $allowed = $this->getAllowedResources();

        $query = LabInventory::with('resource')->whereNull('deleted_at');

        if ($allowed !== null) {
            $query->whereIn('resource_id', $allowed);
        }

        if ($request->filled('resource_id')) {
            $query->where('resource_id', $request->resource_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('item_name', 'like', '%'.$request->search.'%')
                  ->orWhere('brand', 'like', '%'.$request->search.'%')
                  ->orWhere('model', 'like', '%'.$request->search.'%')
                  ->orWhere('specifications', 'like', '%'.$request->search.'%');
            });
        }

        $items = $query->orderBy('resource_id')->orderBy('category')->orderBy('item_name')->get();

        $resQuery = Resource::where('status', 'active')->orderBy('name');
        if ($allowed !== null) $resQuery->whereIn('id', $allowed);
        $resources = $resQuery->get();

        // Stats per lab
        $statsQuery = LabInventory::whereNull('deleted_at');
        if ($allowed !== null) $statsQuery->whereIn('resource_id', $allowed);
        $stats = [
            'total_items'  => (clone $statsQuery)->count(),
            'total_units'  => (clone $statsQuery)->sum('quantity'),
            'total_good'   => (clone $statsQuery)->sum('quantity_good'),
            'total_broken' => (clone $statsQuery)->sum('quantity_broken'),
        ];

        $categories = [
            'computer'   => '🖥 Komputer',
            'peripheral' => '⌨ Peripheral',
            'furniture'  => '🪑 Furnitur',
            'network'    => '🌐 Jaringan',
            'software'   => '💿 Software',
            'other'      => '📦 Lainnya',
        ];

        $conditions = [
            'excellent' => 'Sangat Baik',
            'good'      => 'Baik',
            'fair'      => 'Cukup',
            'poor'      => 'Buruk',
            'broken'    => 'Rusak',
        ];

        return view('inventory.admin', compact(
            'items', 'resources', 'stats', 'categories', 'conditions'
        ));
    }

    public function store(Request $request)
    {
        $allowed = $this->getAllowedResources();
        if ($allowed !== null && !in_array($request->resource_id, $allowed)) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses ke lab ini.'])->withInput();
        }

        $request->validate([
            'resource_id'      => 'required|exists:resources,id',
            'item_name'        => 'required|string|max:255',
            'category'         => 'required|in:computer,peripheral,furniture,network,software,other',
            'brand'            => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100',
            'serial_number'    => 'nullable|string|max:100',
            'specifications'   => 'nullable|string',
            'condition'        => 'required|in:excellent,good,fair,poor,broken',
            'quantity'         => 'required|integer|min:1',
            'quantity_good'    => 'required|integer|min:0',
            'quantity_broken'  => 'required|integer|min:0',
            'quantity_backup'  => 'required|integer|min:0',
            'notes'            => 'nullable|string',
        ]);

        LabInventory::create([
            'resource_id'     => $request->resource_id,
            'item_name'       => $request->item_name,
            'category'        => $request->category,
            'brand'           => $request->brand,
            'model'           => $request->model,
            'serial_number'   => $request->serial_number,
            'specifications'  => $request->specifications,
            'condition'       => $request->condition,
            'status'          => 'active',
            'quantity'        => $request->quantity,
            'quantity_good'   => $request->quantity_good,
            'quantity_broken' => $request->quantity_broken,
            'quantity_backup' => $request->quantity_backup,
            'notes'           => $request->notes,
            'created_by'      => auth()->id(),
        ]);

        return back()->with('success', 'Barang "'.$request->item_name.'" berhasil ditambahkan.');
    }

    public function update(Request $request, LabInventory $inventory)
    {
        $allowed = $this->getAllowedResources();
        if ($allowed !== null && !in_array($inventory->resource_id, $allowed)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $request->validate([
            'item_name'       => 'required|string|max:255',
            'category'        => 'required|in:computer,peripheral,furniture,network,software,other',
            'brand'           => 'nullable|string|max:100',
            'model'           => 'nullable|string|max:100',
            'serial_number'   => 'nullable|string|max:100',
            'specifications'  => 'nullable|string',
            'condition'       => 'required|in:excellent,good,fair,poor,broken',
            'quantity'        => 'required|integer|min:1',
            'quantity_good'   => 'required|integer|min:0',
            'quantity_broken' => 'required|integer|min:0',
            'quantity_backup' => 'required|integer|min:0',
            'notes'           => 'nullable|string',
        ]);

        $inventory->update([
            'item_name'       => $request->item_name,
            'category'        => $request->category,
            'brand'           => $request->brand,
            'model'           => $request->model,
            'serial_number'   => $request->serial_number,
            'specifications'  => $request->specifications,
            'condition'       => $request->condition,
            'quantity'        => $request->quantity,
            'quantity_good'   => $request->quantity_good,
            'quantity_broken' => $request->quantity_broken,
            'quantity_backup' => $request->quantity_backup,
            'notes'           => $request->notes,
            'updated_by'      => auth()->id(),
        ]);

        return back()->with('success', 'Barang "'.$inventory->item_name.'" berhasil diperbarui.');
    }

    public function destroy(LabInventory $inventory)
    {
        $allowed = $this->getAllowedResources();
        if ($allowed !== null && !in_array($inventory->resource_id, $allowed)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $name = $inventory->item_name;
        $inventory->update(['deleted_at' => now()]);
        return back()->with('success', 'Barang "'.$name.'" berhasil dihapus.');
    }
}
