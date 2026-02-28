<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lab_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token', 12)->unique();
            $table->string('lab_key', 20);
            $table->unsignedBigInteger('resource_id');
            $table->enum('source_type', ['booking', 'schedule', 'manual']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('teacher_name');
            $table->string('teacher_phone')->nullable();
            $table->datetime('session_start');
            $table->datetime('session_end');
            $table->datetime('used_at')->nullable();
            $table->datetime('invalidated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('invalidated_reason')->nullable();
            $table->timestamps();

            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            $table->index(['token', 'is_active']);
            $table->index(['resource_id', 'session_start']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('lab_sessions');
    }
};
