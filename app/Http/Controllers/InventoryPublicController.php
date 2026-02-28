<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\LabInventory;

class InventoryPublicController extends Controller
{
    public function index()
    {
        $resources = Resource::where('status', 'active')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $inventories = LabInventory::whereNull('deleted_at')
            ->where('status', 'active')
            ->orderBy('category')
            ->orderBy('item_name')
            ->get();

        $totalItems  = $inventories->count();
        $totalUnits  = $inventories->sum('quantity');
        $totalBroken = $inventories->sum('quantity_broken');

        return view('inventory.public', compact(
            'resources', 'inventories',
            'totalItems', 'totalUnits', 'totalBroken'
        ));
    }
}
