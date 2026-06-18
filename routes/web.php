<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OwnerKaryawanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\StokBahanController;
use App\Models\ActivityLog;
use App\Models\ChatMessage;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\StokBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

function wanaProducts(): array
{
    try {
        return Produk::query()
            ->with('kategori')
            ->where('aktif', true)
            ->latest()
            ->get()
            ->map(fn (Produk $produk) => [
                'id' => $produk->id,
                'name' => $produk->nama,
                'category' => $produk->kategori?->nama ?? 'Menu',
                'price' => (int) $produk->harga,
                'stock' => $produk->stok,
                'description' => $produk->deskripsi,
                'image' => $produk->gambar ?: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80',
            ])
            ->all();
    } catch (Throwable) {
        return [];
    }
}

function wanaOrders(): array
{
    $statusMap = [
        'pending' => 'Masuk',
        'diproses' => 'Diproses',
        'siap' => 'Selesai',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    try {
        return Pesanan::query()
            ->with(['kasir', 'detail'])
            ->latest()
            ->get()
            ->map(fn (Pesanan $pesanan) => [
                'id' => $pesanan->kode_pesanan,
                'table' => $pesanan->no_meja ?: 'Meja -',
                'customer' => $pesanan->nama_pelanggan,
                'cashier' => $pesanan->nama_kasir ?: ($pesanan->kasir?->name ?? 'Kasir'),
                'status' => $statusMap[$pesanan->status] ?? 'Masuk',
                'createdAt' => $pesanan->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '',
                'items' => $pesanan->detail->map(fn ($detail) => [
                    'id' => $detail->produk_id,
                    'name' => $detail->nama_produk,
                    'qty' => $detail->qty,
                    'price' => (int) $detail->harga_saat_itu,
                ])->values()->all(),
                'total' => (int) $pesanan->total_harga,
                'paid' => (int) $pesanan->dibayar,
                'change' => (int) $pesanan->kembalian,
            ])
            ->all();
    } catch (Throwable) {
        return [];
    }
}

function wanaChats(): array
{
    try {
        $userId = Auth::id();

        $query = ChatMessage::query()
            ->with(['fromUser', 'toUser']);

        if ($userId) {
            $query->where(function ($chatQuery) use ($userId) {
                $chatQuery
                    ->where('from_user_id', $userId)
                    ->orWhere('to_user_id', $userId);
            });
        }

        return $query
            ->latest()
            ->limit(100)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $message) => ChatController::serializeChat($message))
            ->values()
            ->all();
    } catch (Throwable) {
        return [];
    }
}

function markWanaChatsRead(Request $request): void
{
    ChatMessage::query()
        ->where('to_user_id', $request->user()->id)
        ->where('dibaca', false)
        ->update(['dibaca' => true]);
}

function wanaMaterials(): array
{
    try {
        return StokBahan::query()
            ->latest()
            ->get()
            ->map(fn (StokBahan $bahan) => [
                'id' => $bahan->id,
                'name' => $bahan->nama,
                'qty' => (float) $bahan->jumlah,
                'unit' => $bahan->satuan,
                'min' => (float) $bahan->stok_minimum,
                'category' => $bahan->kategori ?? 'Bahan Minuman',
                'note' => $bahan->keterangan ?? '',
            ])
            ->all();
    } catch (Throwable) {
        return [];
    }
}

function wanaActivities(): array
{
    try {
        return ActivityLog::query()
            ->with('user')
            ->latest()
            ->limit(120)
            ->get()
            ->map(fn (ActivityLog $log) => ActivityLogController::serialize($log))
            ->all();
    } catch (Throwable) {
        return [];
    }
}

function rolePage(string $role, string $page, array $data = [])
{
    return view("roles.$role.$page", array_merge([
        'products' => wanaProducts(),
        'orders' => wanaOrders(),
        'chats' => wanaChats(),
        'materials' => wanaMaterials(),
        'activities' => wanaActivities(),
    ], $data));
}

function chatPage(Request $request, string $role)
{
    ensureRole($request, $role);
    markWanaChatsRead($request);

    return view('roles.chat', [
        'products' => wanaProducts(),
        'orders' => wanaOrders(),
        'chats' => wanaChats(),
        'materials' => wanaMaterials(),
        'activities' => wanaActivities(),
    ]);
}

function ensureRole(Request $request, string $role): void
{
    abort_unless($request->user()?->role === $role, 403);
}

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
