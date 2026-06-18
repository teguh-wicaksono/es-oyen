<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    private const STATUS_TO_DB = [
        'Masuk' => 'pending',
        'Diproses' => 'diproses',
        'Selesai' => 'selesai',
    ];

    private const STATUS_TO_VIEW = [
        'pending' => 'Masuk',
        'diproses' => 'Diproses',
        'siap' => 'Selesai',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()?->role === 'kasir', 403);

        $data = $request->validate([
            'id' => ['nullable', 'string', 'max:20', 'unique:pesanan,kode_pesanan'],
            'table' => ['nullable', 'string', 'max:20'],
            'customer' => ['required', 'string', 'max:100'],
            'cashier' => ['required', 'string', 'max:100'],
            'paid' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:produk,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $result = DB::transaction(function () use ($request, $data) {
            $productIds = collect($data['items'])->pluck('id')->all();
            $products = Produk::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            foreach ($data['items'] as $item) {
                $produk = $products[$item['id']];
                abort_if($produk->stok < $item['qty'], 422, "Stok {$produk->nama} tidak cukup.");
                $total += (int) $produk->harga * (int) $item['qty'];
            }
            abort_if($data['paid'] < $total, 422, 'Uang pelanggan kurang dari total pesanan.');

            $pesanan = new Pesanan();
            $pesanan->kode_pesanan = $data['id'] ?? $this->nextCode();
            $pesanan->kasir_id = $request->user()->id;
            $pesanan->nama_kasir = $data['cashier'];
            $pesanan->nama_pelanggan = $data['customer'];
            $pesanan->no_meja = $data['table'] ?? null;
            $pesanan->status = 'pending';
            $pesanan->total_harga = $total;
            $pesanan->dibayar = $data['paid'];
            $pesanan->kembalian = $data['paid'] - $total;
            $pesanan->save();

            foreach ($data['items'] as $item) {
                $produk = $products[$item['id']];
                $qty = (int) $item['qty'];
                $harga = (int) $produk->harga;

                $pesanan->detail()->create([
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama,
                    'harga_saat_itu' => $harga,
                    'qty' => $qty,
                    'subtotal' => $harga * $qty,
                ]);

                $produk->stok -= $qty;
                $produk->save();
            }

            User::query()
                ->where('role', 'dapur')
                ->get()
                ->each(fn (User $user) => Notifikasi::query()->create([
                    'user_id' => $user->id,
                    'judul' => "Pesanan baru {$pesanan->kode_pesanan}",
                    'pesan' => "{$pesanan->nama_pelanggan} ({$pesanan->no_meja}) menunggu diproses.",
                    'tipe' => 'pesanan_baru',
                    'dibaca' => false,
                    'url' => route('dapur.status'),
                ]));

            return [
                'order' => $this->serializeOrder($pesanan->load(['kasir', 'detail'])),
                'products' => $this->serializeProducts(),
            ];
        });

        return response()->json($result, 201);
    }

    public function updateStatus(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless($request->user()?->role === 'dapur', 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUS_TO_DB))],
        ]);

        $previousStatus = self::STATUS_TO_VIEW[$pesanan->status] ?? $pesanan->status;
        if ($previousStatus === $data['status']) {
            return response()->json(['order' => $this->serializeOrder($pesanan->load(['kasir', 'detail']))]);
        }

        abort_unless($this->canTransition($previousStatus, $data['status']), 422, "Status hanya bisa berjalan berurutan: Masuk -> Diproses -> Selesai.");

        $pesanan->status = self::STATUS_TO_DB[$data['status']];
        $pesanan->save();
        ActivityLog::query()->create([
            'user_id' => $request->user()->id,
            'tipe' => 'Pesanan',
            'judul' => "Status pesanan {$pesanan->kode_pesanan}: {$data['status']}",
            'detail' => "{$pesanan->nama_pelanggan} ({$pesanan->no_meja}) dari {$previousStatus} menjadi {$data['status']}.",
            'meta' => ['orderId' => $pesanan->kode_pesanan, 'from' => $previousStatus, 'to' => $data['status']],
        ]);

        return response()->json(['order' => $this->serializeOrder($pesanan->load(['kasir', 'detail']))]);
    }

    private function serializeOrder(Pesanan $pesanan): array
    {
        return [
            'id' => $pesanan->kode_pesanan,
            'table' => $pesanan->no_meja ?: 'Meja -',
            'customer' => $pesanan->nama_pelanggan,
            'cashier' => $pesanan->nama_kasir ?: ($pesanan->kasir?->name ?? 'Kasir'),
            'status' => self::STATUS_TO_VIEW[$pesanan->status] ?? 'Masuk',
            'createdAt' => $pesanan->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
            'items' => $pesanan->detail->map(fn ($detail) => [
                'id' => $detail->produk_id,
                'name' => $detail->nama_produk,
                'qty' => $detail->qty,
                'price' => (int) $detail->harga_saat_itu,
            ])->values()->all(),
            'total' => (int) $pesanan->total_harga,
            'paid' => (int) $pesanan->dibayar,
            'change' => (int) $pesanan->kembalian,
        ];
    }

    private function serializeProducts(): array
    {
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
                'image' => $produk->gambar,
            ])
            ->all();
    }

    private function nextCode(): string
    {
        return 'WN-' . now()->format('His');
    }

    private function canTransition(string $from, string $to): bool
    {
        return match ($from) {
            'Masuk' => $to === 'Diproses',
            'Diproses' => $to === 'Selesai',
            default => false,
        };
    }
}
