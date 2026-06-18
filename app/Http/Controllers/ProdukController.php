<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\ActivityLog;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $this->authorizeDapur($request);

        $data = $this->validatedData($request);
        $kategori = Kategori::query()->firstOrCreate(['nama' => $data['category']]);

        $produk = new Produk();
        $produk->kategori_id = $kategori->id;
        $produk->nama = $data['name'];
        $produk->deskripsi = $data['description'] ?? null;
        $produk->harga = $data['price'];
        $produk->stok = $data['stock'];
        $produk->gambar = $data['image'];
        $produk->aktif = true;
        $produk->save();
        $activity = $this->logActivity($request, 'Produk', "Produk baru ditambahkan: {$produk->nama}", "Kategori {$kategori->nama}, stok awal {$produk->stok}, harga Rp " . number_format((float) $produk->harga, 0, ',', '.') . '.');

        return response()->json(['product' => $this->serializeProduct($produk->load('kategori')), 'activity' => ActivityLogController::serialize($activity->load('user'))], 201);
    }

    public function update(Request $request, Produk $produk): JsonResponse
    {
        $this->authorizeDapur($request);

        $data = $this->validatedData($request);
        $kategori = Kategori::query()->firstOrCreate(['nama' => $data['category']]);

        $previousStock = $produk->stok;
        $produk->kategori_id = $kategori->id;
        $produk->nama = $data['name'];
        $produk->deskripsi = $data['description'] ?? null;
        $produk->harga = $data['price'];
        $produk->stok = $data['stock'];
        $produk->gambar = $data['image'];
        $produk->aktif = true;
        $produk->save();
        $activity = $this->logActivity($request, 'Produk', "Produk diperbarui: {$produk->nama}", "Kategori {$kategori->nama}, stok {$previousStock} -> {$produk->stok}, harga Rp " . number_format((float) $produk->harga, 0, ',', '.') . '.');

        return response()->json(['product' => $this->serializeProduct($produk->load('kategori')), 'activity' => ActivityLogController::serialize($activity->load('user'))]);
    }

    public function destroy(Request $request, Produk $produk): JsonResponse
    {
        $this->authorizeDapur($request);

        $produk->aktif = false;
        $produk->save();
        $activity = $this->logActivity($request, 'Produk', "Produk dihapus: {$produk->nama}", "Produk dinonaktifkan dari menu kasir.");

        return response()->json(['ok' => true, 'activity' => ActivityLogController::serialize($activity->load('user'))]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'image' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function serializeProduct(Produk $produk): array
    {
        return [
            'id' => $produk->id,
            'name' => $produk->nama,
            'category' => $produk->kategori?->nama ?? 'Menu',
            'price' => (int) $produk->harga,
            'stock' => $produk->stok,
            'description' => $produk->deskripsi,
            'image' => $produk->gambar,
        ];
    }

    private function authorizeDapur(Request $request): void
    {
        abort_unless($request->user()?->role === 'dapur', 403);
    }

    private function logActivity(Request $request, string $type, string $title, string $detail): ActivityLog
    {
        return ActivityLog::query()->create([
            'user_id' => $request->user()->id,
            'tipe' => $type,
            'judul' => $title,
            'detail' => $detail,
            'meta' => [],
        ]);
    }
}
