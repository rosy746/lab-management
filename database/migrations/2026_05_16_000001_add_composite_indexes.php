<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── resources: composite index untuk query WHERE status = 'active' ORDER BY name ──
        Schema::table('resources', function (Blueprint $table) {
            // Cek dulu apakah index sudah ada (aman untuk re-run)
            $indexes = collect(\DB::select("SHOW INDEX FROM resources"))
                ->pluck('Key_name')
                ->toArray();

            if (!in_array('idx_status_name', $indexes)) {
                $table->index(['status', 'name'], 'idx_status_name');
            }
        });

        // ── lab_inventory: composite index untuk query WHERE status = 'active' AND deleted_at IS NULL ORDER BY category, item_name ──
        Schema::table('lab_inventory', function (Blueprint $table) {
            $indexes = collect(\DB::select("SHOW INDEX FROM lab_inventory"))
                ->pluck('Key_name')
                ->toArray();

            if (!in_array('idx_status_deleted_cat', $indexes)) {
                $table->index(['status', 'deleted_at', 'category', 'item_name'], 'idx_status_deleted_cat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_status_name');
        });

        Schema::table('lab_inventory', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_status_deleted_cat');
        });
    }
};
