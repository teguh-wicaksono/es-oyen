<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OwnerKaryawanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokBahanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/chat/read', [ChatController::class, 'markRead'])->name('chat.read');
    Route::post('/activity-log', [ActivityLogController::class, 'store'])->name('activity-log.store');
    Route::get('/live-notifications-feed', fn () => response()->json([
        'orders' => wanaOrders(),
        'chats' => wanaChats(),
    ]))->name('live-notifications.feed');

    Route::prefix('kasir')->group(function () {
        Route::get('/', fn (Request $request) => tap(rolePage('kasir', 'home'), fn () => ensureRole($request, 'kasir')))->name('kasir');
        Route::get('/chat', fn (Request $request) => chatPage($request, 'kasir'))->name('kasir.chat');
        Route::get('/pesanan', fn (Request $request) => tap(rolePage('kasir', 'pesanan'), fn () => ensureRole($request, 'kasir')))->name('kasir.pesanan');
        Route::post('/pesanan', [PesananController::class, 'store'])->name('kasir.pesanan.store');
        Route::get('/riwayat', fn (Request $request) => tap(rolePage('kasir', 'riwayat'), fn () => ensureRole($request, 'kasir')))->name('kasir.riwayat');
        Route::get('/profil', fn (Request $request) => tap(rolePage('kasir', 'profil'), fn () => ensureRole($request, 'kasir')))->name('kasir.profil');
    });

    Route::prefix('dapur')->group(function () {
        Route::get('/', fn (Request $request) => tap(rolePage('dapur', 'home'), fn () => ensureRole($request, 'dapur')))->name('dapur');
        Route::get('/chat', fn (Request $request) => chatPage($request, 'dapur'))->name('dapur.chat');
        Route::get('/notifikasi', fn (Request $request) => tap(rolePage('dapur', 'notifikasi'), fn () => ensureRole($request, 'dapur')))->name('dapur.notifikasi');
        Route::get('/pesanan-feed', fn (Request $request) => tap(response()->json(['orders' => wanaOrders()]), fn () => ensureRole($request, 'dapur')))->name('dapur.pesanan.feed');
        Route::get('/antrian', fn (Request $request) => tap(rolePage('dapur', 'antrian'), fn () => ensureRole($request, 'dapur')))->name('dapur.antrian');
        Route::get('/status', fn (Request $request) => tap(rolePage('dapur', 'status'), fn () => ensureRole($request, 'dapur')))->name('dapur.status');
        Route::get('/produk', fn (Request $request) => tap(view('roles.dapur.produk', ['products' => wanaProducts(), 'orders' => wanaOrders(), 'chats' => wanaChats(), 'materials' => wanaMaterials(), 'activities' => wanaActivities()]), fn () => ensureRole($request, 'dapur')))->name('dapur.produk');
        Route::post('/produk', [ProdukController::class, 'store'])->name('dapur.produk.store');
        Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('dapur.produk.update');
        Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('dapur.produk.destroy');
        Route::put('/pesanan/{pesanan:kode_pesanan}/status', [PesananController::class, 'updateStatus'])->name('dapur.pesanan.status');
        Route::post('/stok-bahan', [StokBahanController::class, 'store'])->name('dapur.stok-bahan.store');
        Route::put('/stok-bahan/{stokBahan}', [StokBahanController::class, 'update'])->name('dapur.stok-bahan.update');
        Route::patch('/stok-bahan/{stokBahan}/adjust', [StokBahanController::class, 'adjust'])->name('dapur.stok-bahan.adjust');
        Route::delete('/stok-bahan/{stokBahan}', [StokBahanController::class, 'destroy'])->name('dapur.stok-bahan.destroy');
        Route::get('/profil', fn (Request $request) => tap(rolePage('dapur', 'profil'), fn () => ensureRole($request, 'dapur')))->name('dapur.profil');
        Route::get('/stok', fn (Request $request) => tap(rolePage('dapur', 'stok'), fn () => ensureRole($request, 'dapur')))->name('dapur.stok');
        Route::get('/riwayat', fn (Request $request) => tap(rolePage('dapur', 'riwayat'), fn () => ensureRole($request, 'dapur')))->name('dapur.riwayat');
        Route::get('/sync', fn (Request $request) => tap(rolePage('dapur', 'sync'), fn () => ensureRole($request, 'dapur')))->name('dapur.sync');
    });

    Route::prefix('owner')->group(function () {
        Route::get('/', fn (Request $request) => tap(rolePage('owner', 'home'), fn () => ensureRole($request, 'owner')))->name('owner');
        Route::get('/dashboard-feed', fn (Request $request) => tap(response()->json([
            'products' => wanaProducts(),
            'orders' => wanaOrders(),
            'chats' => wanaChats(),
            'materials' => wanaMaterials(),
            'activities' => wanaActivities(),
        ]), fn () => ensureRole($request, 'owner')))->name('owner.dashboard.feed');
        Route::get('/chat', fn (Request $request) => chatPage($request, 'owner'))->name('owner.chat');
        Route::get('/penjualan', fn (Request $request) => tap(rolePage('owner', 'penjualan'), fn () => ensureRole($request, 'owner')))->name('owner.penjualan');
        Route::get('/stok', fn (Request $request) => tap(rolePage('owner', 'stok'), fn () => ensureRole($request, 'owner')))->name('owner.stok');
        Route::get('/stok-bahan', fn (Request $request) => tap(rolePage('owner', 'stok-bahan'), fn () => ensureRole($request, 'owner')))->name('owner.stok-bahan');
        Route::get('/karyawan', fn (Request $request) => tap(rolePage('owner', 'karyawan'), fn () => ensureRole($request, 'owner')))->name('owner.karyawan');
        Route::post('/karyawan', [OwnerKaryawanController::class, 'store'])->name('owner.karyawan.store');
        Route::put('/karyawan/{karyawan}', [OwnerKaryawanController::class, 'update'])->name('owner.karyawan.update');
        Route::delete('/karyawan/{karyawan}', [OwnerKaryawanController::class, 'destroy'])->name('owner.karyawan.destroy');
        Route::get('/export', fn (Request $request) => tap(rolePage('owner', 'export'), fn () => ensureRole($request, 'owner')))->name('owner.export');
        Route::get('/profil', fn (Request $request) => tap(rolePage('owner', 'profil'), fn () => ensureRole($request, 'owner')))->name('owner.profil');
    });
});
