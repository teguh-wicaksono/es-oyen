@extends('layouts.wana', ['title' => 'Sync Dapur | Wana Cafe'])

@section('content')
    <section class="page-head"><div><div class="eyebrow">Dapur</div><h1>Sync Otomatis ke Kasir</h1><p class="lead">Produk dan stok yang diperbarui dapur akan tersedia pada menu kasir di browser yang sama.</p></div><button class="btn" onclick="localStorage.removeItem('wana_products'); location.reload()">Sync Ulang</button></section>
    <div class="table-wrap"><table><thead><tr><th>Produk</th><th>Kategori</th><th>Stok</th><th>Harga</th></tr></thead><tbody>
        @foreach ($products as $product)
            <tr><td><strong>{{ $product['name'] }}</strong></td><td>{{ $product['category'] }}</td><td>{{ $product['stock'] }}</td><td>Rp {{ number_format($product['price'], 0, ',', '.') }}</td></tr>
        @endforeach
    </tbody></table></div>
@endsection
