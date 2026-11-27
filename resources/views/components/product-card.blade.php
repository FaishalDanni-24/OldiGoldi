@props(['product'])
@php
    $discount = $product['old'] > $product['price']
        ? round((1 - $product['price'] / $product['old']) * 100)
        : 0;
@endphp
<article class="product-card">
    <div class="product-photo">
        @if (!empty($product['badge']))
            <span class="badge">{{ $product['badge'] }}</span>
        @endif
        <button type="button" class="heart" aria-label="Simpan ke favorit">
            <svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg>
        </button>
        <button type="button" class="add-cart" {{ $product['ready'] ? '' : 'disabled' }}>
            {{ $product['ready'] ? 'Tambah ke keranjang' : 'Tidak tersedia' }}
        </button>
    </div>
    <div class="product-body">
        <div class="product-meta">
            <span class="cat">{{ $product['cat'] }}</span>
            <span class="rating">&#9733; {{ $product['rating'] }}</span>
        </div>
        <h3>{{ $product['name'] }}</h3>
        <div class="price-row">
            <span class="price">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
            @if ($discount > 0)
                <span class="price-old">Rp {{ number_format($product['old'], 0, ',', '.') }}</span>
                <span class="discount">-{{ $discount }}%</span>
            @endif
        </div>
        <span class="status {{ $product['ready'] ? 'ok' : 'no' }}">{{ $product['ready'] ? 'Tersedia' : 'Tidak tersedia' }}</span>
    </div>
</article>
