@extends('layouts.app')

@section('title', 'Explore | OLDI.')
@section('search', '1')

@section('subnav')
<div class="subnav">
    <a href="#" class="chip active">Semua</a>
    <a href="#" class="chip">Smartphone</a>
    <a href="#" class="chip">Laptop</a>
    <a href="#" class="chip">Kamera</a>
    <a href="#" class="chip">Audio</a>
    <a href="#" class="chip">Komputer</a>
</div>
@endsection

@section('content')
@php
    // Hanya data contoh. Nanti diganti dari database.
    $products = [
        ['badge' => 'Grade A',        'cat' => 'Smartphone', 'rating' => '4.9', 'name' => 'iPhone 12 128GB',          'price' => 6299000,  'old' => 7499000,  'ready' => true],
        ['badge' => 'Like new',       'cat' => 'Laptop',     'rating' => '4.8', 'name' => 'MacBook Pro M1',           'price' => 11499000, 'old' => 13999000, 'ready' => true],
        ['badge' => 'Shutter rendah', 'cat' => 'Kamera',     'rating' => '4.7', 'name' => 'Canon EOS 80D Body',       'price' => 7399000,  'old' => 8299000,  'ready' => true],
        ['badge' => 'Bestseller',     'cat' => 'Audio',      'rating' => '4.6', 'name' => 'Sony WH-1000XM4',          'price' => 2399000,  'old' => 2899000,  'ready' => false],
        ['badge' => 'Grade A',        'cat' => 'Smartphone', 'rating' => '4.8', 'name' => 'Samsung Galaxy S21',       'price' => 5199000,  'old' => 6299000,  'ready' => true],
        ['badge' => null,             'cat' => 'Kamera',     'rating' => '4.9', 'name' => 'Sony A7 III Kit',          'price' => 14990000, 'old' => 18000000, 'ready' => true],
        ['badge' => 'Rare find',      'cat' => 'Komputer',   'rating' => '4.5', 'name' => 'Oscilloscope Tektronix',   'price' => 3450000,  'old' => 3900000,  'ready' => true],
        ['badge' => 'Refurbished',    'cat' => 'Komputer',   'rating' => '4.4', 'name' => 'IBM PC Retro',             'price' => 4850000,  'old' => 5500000,  'ready' => true],
    ];
@endphp

<section class="explore-hero">
    <div class="explore-hero-text">
        <span class="pill-outline">Pilihan terbaru</span>
        <h1>Upgrade teknologimu, bukan pengeluaranmu.</h1>
        <p>Perangkat pilihan yang sudah diperiksa, dibersihkan, dan siap menemani aktivitasmu.</p>
        <a href="#" class="btn btn-white">Lihat koleksi
            <svg viewBox="0 0 24 24" width="16" height="16"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>
</section>

<section class="explore">
    <div class="toolbar">
        <p>Menampilkan <strong>semua kategori</strong></p>
        <div class="toolbar-right">
            <span>{{ count($products) }} produk</span>
            <select aria-label="Urutkan produk">
                <option>Featured</option>
                <option>Harga terendah</option>
                <option>Harga tertinggi</option>
                <option>Terbaru</option>
            </select>
        </div>
    </div>

    <div class="product-grid">
        @foreach ($products as $item)
            <x-product-card :product="$item" />
        @endforeach
    </div>
</section>
@endsection
