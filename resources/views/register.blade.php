<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
<form class="card" onsubmit="return false">
    <h1>Register</h1>
    <p class="subtitle">Buat Akun agar bisa login di web kami.</p>

    <div class="box">
        <h3>Pembuatan Akun</h3>
        <small>Isi semua informasi yang diperlukan</small>

        <div class="field"><i class="bi bi-envelope"></i>
            <input type="email" name="email" placeholder="@gmail">
        </div>
        <div class="field"><i class="bi bi-telephone-fill"></i>
            <input type="text" name="phone" placeholder="Nomor telepon">
        </div>
        <div class="field"><i class="bi bi-lock-fill"></i>
            <input type="password" name="password" placeholder="Password">
        </div>

        <h3 style="margin-top:30px">Social network</h3>
        <small>Isi informasi jaringan sosial yang ingin Anda gunakan</small>

        <div id="social-list">
            <div class="row">
                <div class="field"><i class="bi bi-skype"></i>
                    <select name="social[0][type]">
                        <option>Skype</option>
                        <option>Facebook</option>
                        <option>Instagram</option>
                    </select>
                </div>
                <div class="field">
                    <input type="text" name="social[0][profile]" placeholder="@profile">
                </div>
            </div>
        </div>

        <button type="button" class="add-more" id="add-more">
            <i class="bi bi-plus-lg"></i> Add More
        </button>
    </div>

    <div class="actions">
        <button type="submit" class="btn-next">Go Next →</button>
    </div>
</form>

<script>
    let i = 1;
    document.getElementById('add-more').addEventListener('click', () => {
        document.getElementById('social-list').insertAdjacentHTML('beforeend', `
            <div class="row">
                <div class="field"><i class="bi bi-facebook"></i>
                    <select name="social[${i}][type]">
                        <option>Facebook</option>
                        <option>Skype</option>
                        <option>Instagram</option>
                    </select>
                </div>
                <div class="field">
                    <input type="text" name="social[${i}][profile]" placeholder="@profile">
                </div>
            </div>`);
        i++;
    });
</script>
</body>
</html>