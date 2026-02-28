<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function connection() { return 'finance'; }

    public function up(): void
    {
        Schema::connection('finance')->table('wa_settings', function (Blueprint $table) {
            // Ganti kolom admin_phone (single) → target_phones (JSON array)
            if (Schema::connection('finance')->hasColumn('wa_settings', 'admin_phone')) {
                $table->json('target_phones')->nullable()->after('admin_phone');
            } else {
                $table->json('target_phones')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::connection('finance')->table('wa_settings', function (Blueprint $table) {
            $table->dropColumn('target_phones');
        });
    }
};
