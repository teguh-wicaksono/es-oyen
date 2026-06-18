<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_bahan', function (Blueprint $table) {
            if (! Schema::hasColumn('stok_bahan', 'kategori')) {
                $table->string('kategori', 100)->default('Bahan Minuman')->after('nama');
            }
        });

        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('tipe', 50);
                $table->string('judul', 180);
                $table->text('detail')->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');

        Schema::table('stok_bahan', function (Blueprint $table) {
            if (Schema::hasColumn('stok_bahan', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
