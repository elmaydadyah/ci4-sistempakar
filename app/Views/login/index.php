<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StuntCare</title>
    <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/login.css?v=' . filemtime(FCPATH . 'assets/css/login.css')) ?>" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo_puskesmas.png') ?>">
</head>

<body>
    <main class="login-page">
        <section class="login-card" aria-label="Login StuntCare">
            <div class="login-visual">
                <div class="login-brand">
                    <div class="login-logo-pair">
                        <img src="<?= base_url('assets/images/logo/logokabbogor.png') ?>" alt="Logo Kabupaten Bogor">
                        <img src="<?= base_url('assets/images/logo/logo_puskesmas.png') ?>" alt="Logo Puskesmas">
                    </div>
                </div>
                <div class="login-visual-copy">
                    <h1>Deteksi stunting dini, lebih mudah dan terarah.</h1>
                    <p>Masuk sebagai Admin untuk mengelola data sistem.</p>
                </div>
            </div>

            <div class="login-form-panel">
                <div class="login-form-wrap">
                    <p class="login-kicker">Welcome back</p>
                    <h2>Masuk Akun Sebagai Admin</h2>
                    <p class="login-subtitle">Gunakan email dan password yang sudah terdaftar.</p>

                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                        </div>
                        <div class="login-actions">
                            <button type="submit" class="btn-login">Login</button>
                            <a class="btn-login btn-login-secondary" href="<?= base_url('/') ?>">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>
