<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LoadPsi extends Command
{
    protected $signature = 'app:load-psi';

    protected $description = 'Load the database from psi.sql once (schema + data).';

    public function handle(): int
    {
        if (Schema::hasTable('produk') && DB::table('produk')->count() > 0) {
            $this->info('Database already populated, skipping psi.sql import.');

            return self::SUCCESS;
        }

        $path = base_path('psi.sql');

        if (! file_exists($path)) {
            $this->error('psi.sql not found at '.$path);

            return self::FAILURE;
        }

        $this->info('Importing psi.sql ...');
        Artisan::call('db:wipe', ['--force' => true]);
        DB::unprepared(file_get_contents($path));
        $this->info('Database imported from psi.sql.');

        return self::SUCCESS;
    }
}
