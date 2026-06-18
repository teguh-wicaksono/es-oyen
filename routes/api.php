<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::get('/pull', function (Request $request) {
        $since = $request->query('since');
        $tables = ['users', 'kategori', 'produk', 'pesanan', 'detail_pesanan', 'stok_bahan', 'riwayat_stok', 'chat_messages', 'notifikasi', 'activity_logs'];
        $data = [];

        foreach ($tables as $table) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                $data[$table] = [];
                continue;
            }

            $query = DB::table($table);
            $alwaysFullTables = ['kategori', 'produk'];
            if ($since && ! in_array($table, $alwaysFullTables, true)) {
                if (DB::getSchemaBuilder()->hasColumn($table, 'updated_at')) {
                    $query->where(function ($q) use ($since) {
                        $q->where('updated_at', '>', $since)
                          ->orWhere('created_at', '>', $since);
                    });
                } elseif (DB::getSchemaBuilder()->hasColumn($table, 'created_at')) {
                    $query->where('created_at', '>', $since);
                }
            }

            $data[$table] = $query->get();
        }

        return response()->json([
            'ok' => true,
            'server_time' => now()->format('Y-m-d H:i:s'),
            'data' => $data,
        ]);
    });

    Route::post('/sync', function (Request $request) {
        $entity = $request->string('entity')->toString();
        $action = $request->string('action')->toString();
        $payload = $request->input('payload', []);
        $allowed = ['users', 'kategori', 'produk', 'pesanan', 'detail_pesanan', 'stok_bahan', 'riwayat_stok', 'chat_messages', 'notifikasi', 'activity_logs'];

        abort_unless(in_array($entity, $allowed, true), 422, 'Entity tidak valid');
        abort_unless(DB::getSchemaBuilder()->hasTable($entity), 422, 'Tabel tidak tersedia');

        DB::transaction(function () use ($entity, $action, $payload) {
            if ($entity === 'pesanan') {
                syncMobilePesanan($action, $payload);
                return;
            }

            if ($action === 'delete') {
                if ($entity === 'produk') {
                    DB::table('produk')->where('id', $payload['id'])->update(['aktif' => 0, 'updated_at' => now()]);
                } else {
                    DB::table($entity)->where('id', $payload['id'])->delete();
                }
                return;
            }

            if ($action === 'stock') {
                $id = $payload['id'];
                $delta = $payload['delta'] ?? 0;
                if ($entity === 'produk') {
                    DB::table('produk')->where('id', $id)->update([
                        'stok' => DB::raw('GREATEST(stok + '.(int) $delta.', 0)'),
                        'updated_at' => now(),
                    ]);
                }
                if ($entity === 'stok_bahan') {
                    DB::table('stok_bahan')->where('id', $id)->update([
                        'jumlah' => DB::raw('GREATEST(jumlah + '.(float) $delta.', 0)'),
                        'updated_at' => now(),
                    ]);
                }
                return;
            }

            unset($payload['synced'], $payload['items']);
            $payload = filterMobileColumns($entity, $payload);
            if (array_key_exists('updated_at', $payload) && blank($payload['updated_at'])) {
                $payload['updated_at'] = now();
            }
            if (! array_key_exists('updated_at', $payload) && DB::getSchemaBuilder()->hasColumn($entity, 'updated_at')) {
                $payload['updated_at'] = now();
            }

            $key = isset($payload['id']) ? ['id' => $payload['id']] : $payload;
            DB::table($entity)->updateOrInsert($key, $payload);
        });

        return response()->json(['ok' => true]);
    });
});

if (! function_exists('syncMobilePesanan')) {
function syncMobilePesanan(string $action, array $payload): void
{
    if ($action === 'status') {
        DB::table('pesanan')
            ->where('kode_pesanan', $payload['kode_pesanan'])
            ->update(['status' => $payload['status'], 'updated_at' => now()]);
        return;
    }

    if ($action === 'delete') {
        DB::table('pesanan')->where('id', $payload['id'])->delete();
        return;
    }

    $items = $payload['items'] ?? [];
    unset($payload['items'], $payload['synced']);

    $payload['kasir_id'] = validMobileUserId($payload['kasir_id'] ?? null, 'kasir');
    $payload['nama_kasir'] = $payload['nama_kasir'] ?? DB::table('users')->where('id', $payload['kasir_id'])->value('name') ?? 'Kasir';
    $payload['updated_at'] = $payload['updated_at'] ?? now();
    $payload['created_at'] = $payload['created_at'] ?? now();

    $orderData = filterMobileColumns('pesanan', $payload);
    DB::table('pesanan')->updateOrInsert(['kode_pesanan' => $orderData['kode_pesanan']], $orderData);

    $pesananId = DB::table('pesanan')->where('kode_pesanan', $orderData['kode_pesanan'])->value('id');
    if (! $pesananId || empty($items)) {
        return;
    }

    DB::table('detail_pesanan')->where('pesanan_id', $pesananId)->delete();
    foreach ($items as $item) {
        $produkId = $item['produk_id'];
        $qty = (int) ($item['qty'] ?? 1);
        $harga = (int) ($item['harga'] ?? DB::table('produk')->where('id', $produkId)->value('harga') ?? 0);
        $nama = $item['nama_produk'] ?? DB::table('produk')->where('id', $produkId)->value('nama') ?? 'Produk';

        DB::table('detail_pesanan')->insert([
            'pesanan_id' => $pesananId,
            'produk_id' => $produkId,
            'nama_produk' => $nama,
            'harga_saat_itu' => $harga,
            'qty' => $qty,
            'subtotal' => $harga * $qty,
            'created_at' => now(),
        ]);
    }
}

} // end syncMobilePesanan

if (! function_exists('validMobileUserId')) {
function validMobileUserId(mixed $id, string $role): int
{
    if ($id && DB::table('users')->where('id', $id)->exists()) {
        return (int) $id;
    }

    return (int) DB::table('users')->where('role', $role)->value('id')
        ?: (int) DB::table('users')->value('id');
}

} // end validMobileUserId

if (! function_exists('filterMobileColumns')) {
function filterMobileColumns(string $table, array $payload): array
{
    $columns = DB::getSchemaBuilder()->getColumnListing($table);
    return collect($payload)
        ->only($columns)
        ->reject(fn ($value) => is_array($value))
        ->all();
}
} // end filterMobileColumns
