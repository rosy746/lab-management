<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Resource;
use App\Models\LabInventory;

class InventoryPublicController extends Controller
{
    public function index()
    {
        // Cache resources 5 menit — jarang berubah
        $resources = Cache::remember('active_resources', 300, function () {
            return Resource::where('status', 'active')
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'building', 'capacity', 'status']);
        });

        // Cache inventories 5 menit — jarang berubah real-time
        $inventories = Cache::remember('active_inventories', 300, function () {
            return LabInventory::whereNull('deleted_at')
                ->where('status', 'active')
                ->orderBy('category')
                ->orderBy('item_name')
                ->get([
                    'id', 'resource_id', 'item_name', 'category',
                    'brand', 'model', 'specifications', 'condition',
                    'quantity', 'quantity_good', 'quantity_broken',
                    'quantity_backup', 'notes',
                ]);
        });

        // Hitung stats dari collection — tidak perlu query tambahan
        $totalItems  = $inventories->count();
        $totalUnits  = $inventories->sum('quantity');
        $totalBroken = $inventories->sum('quantity_broken');

        return view('inventory.public', compact(
            'resources', 'inventories',
            'totalItems', 'totalUnits', 'totalBroken'
        ));
    }
}