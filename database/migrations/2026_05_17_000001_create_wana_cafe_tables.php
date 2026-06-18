<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 100);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('produk')) {
            Schema::create('produk', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
                $table->string('nama', 150);
                $table->text('deskripsi')->nullable();
                $table->decimal('harga', 12, 2)->default(0);
                $table->integer('stok')->default(0);
                $table->string('gambar')->nullable();
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pesanan')) {
            Schema::create('pesanan', function (Blueprint $table) {
                $table->id();
                $table->string('kode_pesanan', 20)->unique();
                $table->foreignId('kasir_id')->constrained('users')->restrictOnDelete();
                $table->string('nama_pelanggan', 100);
                $table->string('no_meja', 20)->nullable();
                $table->text('catatan')->nullable();
                $table->enum('status', ['pending', 'diproses', 'siap', 'selesai', 'dibatalkan'])->default('pending');
                $table->decimal('total_harga', 12, 2)->default(0);
                $table->decimal('dibayar', 12, 2)->default(0);
                $table->decimal('kembalian', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('detail_pesanan')) {
            Schema::create('detail_pesanan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
                $table->foreignId('produk_id')->constrained('produk')->restrictOnDelete();
                $table->string('nama_produk', 150);
                $table->decimal('harga_saat_itu', 12, 2);
                $table->integer('qty')->default(1);
                $table->decimal('subtotal', 12, 2);
                $table->string('catatan_item')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('stok_bahan')) {
            Schema::create('stok_bahan', function (Blueprint $table) {
                $table->id();
                $table->string('nama', 150);
                $table->decimal('jumlah', 10, 2)->default(0);
                $table->string('satuan', 30)->default('pcs');
                $table->decimal('stok_minimum', 10, 2)->default(5);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('riwayat_stok')) {
            Schema::create('riwayat_stok', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bahan_id')->constrained('stok_bahan')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
                $table->enum('jenis', ['masuk', 'keluar']);
                $table->decimal('jumlah', 10, 2);
                $table->string('keterangan')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
                $table->text('pesan');
                $table->boolean('dibaca')->default(false);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('judul', 150);
                $table->text('pesan');
                $table->enum('tipe', ['pesanan_baru', 'pesanan_siap', 'stok_habis', 'chat', 'info'])->default('info');
                $table->boolean('dibaca')->default(false);
                $table->string('url')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('riwayat_stok');
        Schema::dropIfExists('stok_bahan');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori');
    }
};
