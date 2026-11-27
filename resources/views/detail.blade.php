@extends('layouts.app')

@section('title', 'MacBook Pro M1 | OLDI.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endpush

@section('content')
@php
    // Data contoh. Nanti diganti dari database.
    $product = [
        'id' => 'macbook-pro-m1', 'name' => 'MacBook Pro M1', 'cat' => 'Laptop',
        'rating' => '4.8', 'reviews' => 94,
        'price' => 11499000, 'old' => 13999000, 'ready' => true,
        'desc' => 'MacBook Pro M1 dengan performa cepat dan baterai tahan lama. Unit telah melalui pengecekan keyboard, layar, port, speaker, dan kesehatan baterai.',
    ];
    $discount = round((1 - $product['price'] / $product['old']) * 100);
    $warranty = ['90 hari', '6 bulan', '12 bulan'];
    $reviews = [
        ['name' => 'Alif Ramadhan', 'text' => 'Kualitasnya benar-benar melebihi ekspektasi. Packing rapi dan pengirimannya juga cepat.'],
        ['name' => 'Nadia Putri',   'text' => 'Kondisi barang sesuai deskripsi, semua fungsi normal, dan baterainya masih sehat. Worth it!'],
    ];
    $waText = rawurlencode('Halo OLDI, saya tertarik dengan ' . $product['name']);
@endphp

<section class="detail">
    <a href="{{ url('/explore') }}" class="back-link">
        <svg viewBox="0 0 24 24"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
        Kembali ke explore
    </a>

    <div class="detail-grid">
        {{-- Galeri --}}
        <div class="gallery">
            <div class="thumbs">
                @foreach ([1, 2, 3] as $n)
                <div class="thumb {{ $n === 1 ? 'active' : '' }}">
                    <span class="ph"><svg viewBox="0 0 24 24" width="28" height="28"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M2 20h20"/></svg></span>
                </div>
                @endforeach
            </div>
            <div class="gallery-main ph">
                <svg viewBox="0 0 24 24" width="96" height="96"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M2 20h20"/></svg>
            </div>
        </div>

        {{-- Info --}}
        <div class="detail-info">
            <p class="detail-eyebrow">{{ $product['cat'] }} / Bekas terverifikasi</p>
            <h1>{{ $product['name'] }}</h1>

            <div class="detail-rating">
                <div class="rating-left">
                    <strong>&#9733; {{ $product['rating'] }}</strong>
                    <span>{{ $product['reviews'] }} ulasan</span>
                </div>

                <div class="seller-contact">
                    <span>Hubungi penjual</span>
                    <a href="https://wa.me/6281234567890?text={{ $waText }}" target="_blank" rel="noopener" class="contact-icon" data-tip="Chat WhatsApp" aria-label="Chat penjual lewat WhatsApp">
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                            <path fill="#25d366" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            <path fill="#fff" transform="translate(6.2 6.2) scale(.48)" d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </a>
                    <a href="https://instagram.com/oldi.electronics" target="_blank" rel="noopener" class="contact-icon" data-tip="Instagram" aria-label="Instagram OLDI">
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                            <defs>
                                <linearGradient id="ig-grad-d" x1="0" y1="1" x2="1" y2="0">
                                    <stop offset="0" stop-color="#fdb43c"/>
                                    <stop offset=".45" stop-color="#e1306c"/>
                                    <stop offset="1" stop-color="#5b51d8"/>
                                </linearGradient>
                            </defs>
                            <rect width="24" height="24" rx="6.5" fill="url(#ig-grad-d)"/>
                            <rect x="6" y="6" width="12" height="12" rx="3.6" fill="none" stroke="#fff" stroke-width="1.6"/>
                            <circle cx="12" cy="12" r="2.9" fill="none" stroke="#fff" stroke-width="1.6"/>
                            <circle cx="16.3" cy="7.7" r=".9" fill="#fff"/>
                        </svg>
                    </a>
                    <a href="skype:oldi.support?chat" class="contact-icon" data-tip="Skype" aria-label="Skype OLDI">
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                            <circle cx="12" cy="12" r="11" fill="#00aff0"/>
                            <text x="12" y="17" text-anchor="middle" font-size="14" font-weight="800" font-family="Arial, sans-serif" fill="#fff">S</text>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="detail-price">
                <span class="price">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                <span class="price-old">Rp {{ number_format($product['old'], 0, ',', '.') }}</span>
                <span class="discount">-{{ $discount }}%</span>
            </div>
            <span class="status {{ $product['ready'] ? 'ok' : 'no' }}">{{ $product['ready'] ? 'Tersedia' : 'Tidak tersedia' }}</span>

            <p class="detail-desc">{{ $product['desc'] }}</p>

            <div class="opt-head">
                <span>Pilih perlindungan</span>
                <a href="#">Detail garansi</a>
            </div>
            <div class="opts">
                @foreach ($warranty as $i => $w)
                <span class="opt {{ $i === 0 ? 'active' : '' }}">{{ $w }}</span>
                @endforeach
            </div>

            <div class="detail-actions">
                <button type="button" class="detail-add" {{ $product['ready'] ? '' : 'disabled' }}>
                    Tambah ke keranjang
                    <svg viewBox="0 0 24 24"><path d="M6 8h12l1 13H5L6 8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
                </button>
                <button type="button" class="fav-btn" aria-label="Simpan ke favorit">
                    <svg viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg>
                </button>
            </div>

            <div class="perks">
                <div class="perk">
                    <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <div><strong>Pengiriman cepat</strong><span>Estimasi 2&ndash;4 hari</span></div>
                </div>
                <div class="perk">
                    <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    <div><strong>Garansi OLDI</strong><span>90 hari perlindungan</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="reviews">
    <div class="reviews-inner">
        <div class="reviews-summary">
            <p class="eyebrow">Reviews</p>
            <h2>Dicintai banyak orang.</h2>
            <div class="score">
                <strong>{{ $product['rating'] }}</strong>
                <div>
                    <span class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    <small>{{ $product['reviews'] }} ulasan terverifikasi</small>
                </div>
            </div>
        </div>

        <div class="review-list">
            @foreach ($reviews as $r)
            <article class="review-card">
                <div class="review-head">
                    <h3>{{ $r['name'] }}</h3>
                    <span class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                </div>
                <p>{{ $r['text'] }}</p>
                <small>Pembelian terverifikasi</small>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endsection