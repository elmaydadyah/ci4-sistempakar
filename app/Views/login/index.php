<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            height: 100vh;
        }
        .login-card {
            border-radius: 15px;
            overflow: hidden;
        }
        .login-left {
            background: url('https://source.unsplash.com/600x800/?technology') center;
            background-size: cover;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container h-100">
    <div class="row justify-content-center align-items-center h-100">
        
        <div class="col-md-10 col-lg-8">
            <div class="card login-card shadow-lg">

                <div class="row g-0">
                    
                    <!-- Left Image -->
                    <div class="col-md-6 d-none d-md-block login-left"></div>

                    <!-- Right Form -->
                    <div class="col-md-6 p-5">
                        <h3 class="text-center mb-4">Welcome Back 👋</h3>

                        <form action="" method="post">

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" placeholder="Masukkan email">
                            </div>

                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" class="form-control" placeholder="Masukkan password">
                            </div>

                            <div class="d-grid mb-3">
                                <button class="btn btn-primary">Login</button>
                            </div>

                            <div class="text-center">
                                <small>Belum punya akun? <a href="#">Daftar</a></small>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>