<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // PIN 6 digit, nullable — kelas lama tidak wajib punya PIN dulu
            $table->char('pin', 6)->nullable()->after('name')->unique();
        });

        // Generate PIN unik untuk semua kelas yang sudah ada
        DB::table('classes')->get(['id'])->each(function ($class) {
            $pin = $this->generateUniquePin();
            DB::table('classes')->where('id', $class->id)->update(['pin' => $pin]);
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropUnique(['pin']);
            $table->dropColumn('pin');
        });
    }

    private function generateUniquePin(): string
    {
        do {
            $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (DB::table('classes')->where('pin', $pin)->exists());

        return $pin;
    }
};
