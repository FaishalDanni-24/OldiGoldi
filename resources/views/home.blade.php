@extends('layouts.app')

@section('title', 'OLDI. | Teknologi Bekas Berkualitas')

@section('content')


{{-- 1. Hero --}}
<section class="hero">
    <div class="hero-text">
        <span class="pill-dot">Teknologi layak pakai</span>
        <h1>Teknologi hebat, <span class="accent">harga bersahabat.</span></h1>
        <p>Elektronik bekas berkualitas yang telah diperiksa teknisi. Lebih hemat untukmu, lebih baik untuk bumi.</p>
        <div class="hero-actions">
            <a href="#" class="btn btn-white">Cari perangkat
                <svg viewBox="0 0 24 24" width="18" height="18"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a href="#" class="btn btn-ghost">Lihat bestseller</a>
        </div>
        <div class="hero-proof">
            <span>4.9/5 dari 2K+ pembeli</span>
            <i></i>
            <span>30+ titik pemeriksaan</span>
        </div>
    </div>
    <div class="hero-media">
        <div class="glass-card">
            <div>
                <small>Pilihan teknisi</small>
                <strong>Laptop &amp; kamera terkurasi</strong>
                <span>Diuji, dibersihkan, siap dipakai.</span>
            </div>
            <a href="#" class="arrow" aria-label="Lihat koleksi">
                <svg viewBox="0 0 24 24" width="18" height="18"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- 2. Produk pilihan --}}
@php
    $products = [
        ['badge' => 'Grade A',        'cat' => 'Smartphone', 'rating' => '4.9', 'name' => 'iPhone 12 128GB',       'price' => 6299000,  'old' => 7499000,  'ready' => true],
        ['badge' => 'Like new',       'cat' => 'Laptop',     'rating' => '4.8', 'name' => 'MacBook Pro M1',        'price' => 11499000, 'old' => 13999000, 'ready' => true],
        ['badge' => 'Shutter rendah', 'cat' => 'Kamera',     'rating' => '4.7', 'name' => 'Canon EOS 80D Body',    'price' => 7399000,  'old' => 8299000,  'ready' => true],
        ['badge' => 'Bestseller',     'cat' => 'Audio',      'rating' => '4.6', 'name' => 'Sony WH-1000XM4',       'price' => 2399000,  'old' => 2899000,  'ready' => false],
    ];
@endphp

<section class="products">
    <div class="section-head">
        <div>
            <p class="eyebrow">Pilihan teknisi minggu ini</p>
            <h2>Secondhand, first choice.</h2>
        </div>
        <a href="#" class="link-arrow">Lihat semua
            <svg viewBox="0 0 24 24" width="18" height="18"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>

    <div class="product-grid">
        @foreach ($products as $item)
            <x-product-card :product="$item" />
        @endforeach
    </div>
</section>

{{-- 3. OLDI Checked --}}
<section class="checked">
    <div class="checked-inner">
        <div class="checked-photo">
            <span class="badge-white">OLDI Checked</span>
        </div>
        <div class="checked-text">
            <p class="eyebrow">Better tech, longer life</p>
            <h2>Performa panjang, jejak lebih ringan.</h2>
            <p>Setiap perangkat layak mendapatkan kesempatan kedua. Teknisi kami memeriksa, membersihkan, dan memastikan semuanya siap menemani keseharianmu.</p>
            <div class="stats">
                <div><strong>30+</strong><span>Titik pemeriksaan</span></div>
                <div><strong>12K+</strong><span>Perangkat terselamatkan</span></div>
                <div><strong>90 hari</strong><span>Garansi OLDI</span></div>
            </div>
            <a href="#" class="link-arrow">Kenali koleksi kami
                <svg viewBox="0 0 24 24" width="18" height="18"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- 4. Kontak --}}
<section class="contacts">
    <div class="contacts-box">
        <h2>Our Contacts</h2>
        <p>Hubungi kami</p>

        <div class="contact-grid">
            <a href="#" class="contact-card">
                <div class="logo-box ig">
                    <svg viewBox="0 0 24 24" width="96" height="96" aria-hidden="true">
                        <defs>
                            <linearGradient id="ig-grad" x1="0" y1="1" x2="1" y2="0">
                                <stop offset="0" stop-color="#fdb43c"/>
                                <stop offset=".45" stop-color="#e1306c"/>
                                <stop offset="1" stop-color="#5b51d8"/>
                            </linearGradient>
                        </defs>
                        <rect width="24" height="24" rx="6.5" fill="url(#ig-grad)"/>
                        <rect x="6" y="6" width="12" height="12" rx="3.6" fill="none" stroke="#fff" stroke-width="1.6"/>
                        <circle cx="12" cy="12" r="2.9" fill="none" stroke="#fff" stroke-width="1.6"/>
                        <circle cx="16.3" cy="7.7" r=".9" fill="#fff"/>
                    </svg>
                </div>
                <h3>Instagram</h3>
            </a>

            <a href="#" class="contact-card">
                <div class="logo-box wa">
                    <svg viewBox="0 0 24 24" width="96" height="96" aria-hidden="true">
                        <path fill="#25d366" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        <path fill="#fff" transform="translate(6.2 6.2) scale(.48)" d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                <h3>WhatsApp</h3>
            </a>

            <a href="#" class="contact-card">
                <div class="logo-box sk">
                    <svg viewBox="0 0 24 24" width="96" height="96" aria-hidden="true">
                        <circle cx="12" cy="12" r="11" fill="#00aff0"/>
                        <text x="12" y="17" text-anchor="middle" font-size="14" font-weight="800" font-family="Arial, sans-serif" fill="#fff">S</text>
                    </svg>
                </div>
                <h3>Skype</h3>
            </a>
        </div>
    </div>
</section>


@endsection
