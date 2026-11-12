<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Explore</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/explore.css') }}">
</head>
<body>


<header class="topbar">
    <a href="#" class="gear"><i class="bi bi-gear-fill"></i></a>

    <form class="search" onsubmit="return false">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Laptop Macbook, PS4 Pro, ...">
    </form>

    <button type="button" class="filter-btn"><i class="bi bi-sliders"></i></button>
    <a href="#" class="avatar"><i class="bi bi-person-fill"></i></a>
</header>

<main class="container">
    <div class="banner"></div>

    <section class="product-grid">
        <div class="product-card">
            <div class="thumb"><i class="bi bi-camera"></i></div>
            <h3>Kamera</h3>
            <p class="status">Status: <span class="available">Available (3 units)</span></p>
            <p class="price">1.000.000</p>
            <a href="#" class="btn-detail">Detail</a>
        </div>
    </section>
</main>


{{-- ketika sudah ada produk untuk ditampilkan dan digunain
<header class="topbar">
    <a href="#" class="gear"><i class="bi bi-gear-fill"></i></a>

    <form class="search" method="GET" action="/explore">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Laptop Macbook, PS4 Pro, ..." value="{{ request('q') }}">
    </form>

    <button type="button" class="filter-btn"><i class="bi bi-sliders"></i></button>

    <a href="#" class="avatar"><i class="bi bi-person-fill"></i></a>
</header>

<main class="container">

    <div class="banner">
        <img src="{{ asset('images/banner.jpg') }}" alt="Banner">
    </div>

    <section class="product-grid">
        @foreach ($products as $product)
            <div class="product-card">
                <div class="thumb">
                    <img src="{{ asset('images/' . $product['image']) }}" alt="{{ $product['name'] }}">
                </div>

                <h3>{{ $product['name'] }}</h3>

                <p class="status">
                    Status:
                    @if ($product['stock'] > 0)
                        <span class="available">Available ({{ $product['stock'] }} units)</span>
                    @else
                        <span class="not-available">Not Available</span>
                    @endif
                </p>

                <p class="price">{{ number_format($product['price'], 0, ',', '.') }}</p>

                <a href="#" class="btn-detail">Detail</a>
            </div>
        @endforeach
    </section>

</main>
--}}
</body>
</html>
