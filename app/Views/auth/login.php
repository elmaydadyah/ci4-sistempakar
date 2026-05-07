<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>StuntCare</title>
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/logo/logo.png') ?>">
</head>

<body>
    <main class="login-page">
        <section class="login-card" aria-label="Login StuntCare">
            <div class="login-visual">
                <div class="login-brand">
                    <img src="<?= base_url('assets/images/logo/logo.png') ?>" alt="Logo StuntCare">
                    <strong>StuntCare</strong>
                    <small>Deteksi Dini, Cegah Stunting<br>Wujudkan Generasi Sehat</small>
                </div>
                <div class="login-visual-copy">
                    <h1>Deteksi stunting dini, lebih mudah dan terarah.</h1>
                    <p>Masuk untuk melanjutkan konsultasi dan mengelola data sistem.</p>
                </div>
            </div>

            <div class="login-form-panel">
                <div class="login-form-wrap">
                    <p class="login-kicker">Welcome back</p>
                    <h2>Masuk Akun</h2>
                    <p class="login-subtitle">Gunakan email dan password yang sudah terdaftar.</p>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

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
                        <button type="submit" class="btn-login">Login</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>
