<?php

namespace App\Console\Commands;

use App\Models\LabClass;
use Illuminate\Console\Command;

class GenerateClassPins extends Command
{
    protected $signature   = 'class:generate-pins {--force : Regenerate PIN untuk semua kelas, termasuk yang sudah punya PIN}';
    protected $description = 'Generate PIN unik untuk semua kelas yang belum punya PIN';

    public function handle(): void
    {
        $query = LabClass::where('is_active', true)->whereNull('deleted_at');

        if (!$this->option('force')) {
            $query->whereNull('pin');
        }

        $classes = $query->get(['id', 'name', 'pin']);

        if ($classes->isEmpty()) {
            $this->info('Semua kelas sudah punya PIN.');
            return;
        }

        $this->info("Generating PIN untuk {$classes->count()} kelas...");

        $rows = [];
        foreach ($classes as $class) {
            $pin = LabClass::generateUniquePin();
            $class->update(['pin' => $pin]);
            $rows[] = [$class->name, $pin];
        }

        $this->table(['Kelas', 'PIN'], $rows);
        $this->info('✓ Selesai! Bagikan PIN ke guru masing-masing kelas.');
    }
}