@extends('layouts.wana', ['title' => 'Notifikasi Dapur | Wana Cafe'])

@section('content')
    <section class="page-head"><div><div class="eyebrow">Dapur</div><h1>Notifikasi Pesanan Real-time</h1><p class="lead">Pesanan baru dari kasir ditampilkan sebagai notifikasi operasional.</p></div><span class="pill" id="notifCount">0 notifikasi</span></section>
    <div class="panel"><div id="notifList" class="order-list"></div></div>
@endsection

@push('scripts')
<script>
    const orders=getOrders().filter((order)=>order.status!=='Selesai');
    document.getElementById('notifCount').textContent=`${orders.length} notifikasi`;
    document.getElementById('notifList').innerHTML=orders.map((order)=>`<div class="mini-item"><div><strong>Pesanan baru ${order.id}</strong><span>${order.customer} - ${order.items.map((item)=>`${item.qty}x ${item.name}`).join(', ')}</span></div><span>${order.createdAt}</span></div>`).join('') || '<div class="empty">Belum ada notifikasi pesanan.</div>';
</script>
@endpush
