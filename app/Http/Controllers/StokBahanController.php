<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\RiwayatStok;
use App\Models\StokBahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StokBahanController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $this->authorizeDapur($request);
        $data = $this->validatedData($request);

        $bahan = StokBahan::query()->create([
            'nama' => $data['name'],
            'kategori' => $data['category'],
            'jumlah' => $data['qty'],
            'satuan' => $data['unit'],
            'stok_minimum' => $data['min'],
            'keterangan' => $data['note'] ?? null,
        ]);

        $activity = $this->logActivity($request, 'Stok', "Bahan baru ditambahkan: {$bahan->nama}", "{$bahan->kategori}, jumlah awal {$bahan->jumlah} {$bahan->satuan}.");

        return response()->json(['material' => $this->serialize($bahan), 'activity' => ActivityLogController::serialize($activity->load('user'))], 201);
    }

    public function update(Request $request, StokBahan $stokBahan): JsonResponse
    {
        $this->authorizeDapur($request);
        $data = $this->validatedData($request);
        $previous = $stokBahan->replicate();

        $stokBahan->update([
            'nama' => $data['name'],
            'kategori' => $data['category'],
            'jumlah' => $data['qty'],
            'satuan' => $data['unit'],
            'stok_minimum' => $data['min'],
            'keterangan' => $data['note'] ?? null,
        ]);

        $activity = $this->logActivity(
            $request,
            'Stok',
            "Bahan diperbarui: {$stokBahan->nama}",
            "{$stokBahan->kategori}, jumlah {$previous->jumlah} {$previous->satuan} -> {$stokBahan->jumlah} {$stokBahan->satuan}."
        );

        return response()->json(['material' => $this->serialize($stokBahan), 'activity' => ActivityLogController::serialize($activity->load('user'))]);
    }

    public function adjust(Request $request, StokBahan $stokBahan): JsonResponse
    {
        $this->authorizeDapur($request);

        $data = $request->validate([
            'delta' => ['required', 'numeric'],
        ]);

        $delta = (float) $data['delta'];
        $previous = (float) $stokBahan->jumlah;
        $stokBahan->jumlah = max(0, $previous + $delta);
        $stokBahan->save();

        RiwayatStok::query()->create([
            'bahan_id' => $stokBahan->id,
            'user_id' => $request->user()->id,
            'jenis' => $delta >= 0 ? 'masuk' : 'keluar',
            'jumlah' => abs($delta),
            'keterangan' => $delta >= 0 ? 'Restock cepat dari dashboard' : 'Pemakaian cepat dari dashboard',
        ]);

        $activity = $this->logActivity(
            $request,
            'Stok',
            $delta >= 0 ? "Restock bahan: {$stokBahan->nama}" : "Pemakaian bahan: {$stokBahan->nama}",
            "{$previous} {$stokBahan->satuan} -> {$stokBahan->jumlah} {$stokBahan->satuan}."
        );

        return response()->json(['material' => $this->serialize($stokBahan), 'activity' => ActivityLogController::serialize($activity->load('user'))]);
    }

    public function destroy(Request $request, StokBahan $stokBahan): JsonResponse
    {
        $this->authorizeDapur($request);
        $name = $stokBahan->nama;
        $detail = "{$stokBahan->kategori}, sisa terakhir {$stokBahan->jumlah} {$stokBahan->satuan}.";
        $stokBahan->delete();

        $activity = $this->logActivity($request, 'Stok', "Bahan dihapus: {$name}", $detail);

        return response()->json(['ok' => true, 'activity' => ActivityLogController::serialize($activity->load('user'))]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'qty' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
            'min' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);
    }

    private function serialize(StokBahan $bahan): array
    {
        return [
            'id' => $bahan->id,
            'name' => $bahan->nama,
            'qty' => (float) $bahan->jumlah,
            'unit' => $bahan->satuan,
            'min' => (float) $bahan->stok_minimum,
            'category' => $bahan->kategori ?? 'Bahan Minuman',
            'note' => $bahan->keterangan ?? '',
        ];
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

    private function authorizeDapur(Request $request): void
    {
        abort_unless($request->user()?->role === 'dapur', 403);
    }
}
