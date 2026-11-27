<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'OLDI.')</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @stack('styles')
</head>
<body>

<header class="site-header">
    <div class="navbar">
        <nav class="navbar-left">
            <a href="{{ url('/') }}" class="logo">OLDI<span>.</span></a>
            <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="{{ url('/explore') }}" class="nav-link {{ request()->is('explore') ? 'active' : '' }}">Explore</a>
            <a href="#" class="nav-link">About</a>
        </nav>

        @hasSection('search')
        <label class="nav-search">
            <svg viewBox="0 0 24 24" width="17" height="17"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            <input type="search" placeholder="Cari smartphone, laptop, kamera..." aria-label="Cari produk">
        </label>
        @endif

        <div class="navbar-right">
            <a href="#" class="nav-login">
                <svg viewBox="0 0 24 24" width="18" height="18"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                Login
            </a>
            <button type="button" class="nav-bag" aria-label="Keranjang">
                <svg viewBox="0 0 24 24" width="18" height="18"><path d="M6 8h12l1 13H5L6 8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
            </button>
        </div>
    </div>

    @yield('subnav')
</header>

<main>
    @yield('content')
</main>

</body>
</html>
