<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
<form class="card" onsubmit="return false">
    <div class="title">Login</div>

    <input class="input" type="text" name="username" placeholder="Username">
    <input class="input" type="password" name="password" placeholder="Password">

    <p class="info">Belum ada akun? Buat <a href="/register">Akun</a></p>

    <button type="submit" class="btn-masuk">Masuk</button>
</form>
</body>
</html>