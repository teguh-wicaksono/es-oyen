<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ChatController;
use App\Models\ActivityLog;
use App\Models\ChatMessage;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\StokBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

if (! function_exists('wanaProducts')) {
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
}

if (! function_exists('wanaOrders')) {
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
}

if (! function_exists('wanaChats')) {
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
}

if (! function_exists('markWanaChatsRead')) {
    function markWanaChatsRead(Request $request): void
    {
        ChatMessage::query()
            ->where('to_user_id', $request->user()->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);
    }
}

if (! function_exists('wanaMaterials')) {
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
}

if (! function_exists('wanaActivities')) {
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
}

if (! function_exists('rolePage')) {
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
}

if (! function_exists('chatPage')) {
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
}

if (! function_exists('ensureRole')) {
    function ensureRole(Request $request, string $role): void
    {
        abort_unless($request->user()?->role === $role, 403);
    }
}
