<?php
// views/login.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Sistem Penitipan Hewan</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta1/dist/css/adminlte.min.css">

    <style>
        /* Custom CSS */
        body.login-page {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #0265FE !important; 
        }
        .login-box {
            width: 360px;
        }
        .login-title {
            font-size: 1.5rem; 
            font-weight: 500; 
            color: #212529; 
            text-decoration: none;
        }
    </style>
</head>
<body class="login-page">

<div class="login-box">
    
    <div class="card card-outline card-primary shadow-lg border-0">
        <div class="card-header text-center py-3">
            <a href="#" class="login-title">
                Sistem Penitipan Hewan
            </a>
        </div>
        
        <div class="card-body p-4">
            <p class="login-box-msg text-muted">Silakan login untuk masuk sistem</p>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?= $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div id="alert-container"></div>

            <form id="loginForm" action="index.php?action=login" method="post">
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control form-control-lg bg-light" placeholder="Username" required autofocus>
                    <div class="input-group-text bg-light border-start-0 text-muted">
                        <span class="bi bi-person-fill"></span>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control form-control-lg bg-light" placeholder="Password" required>
                    <div class="input-group-text bg-light border-start-0 text-muted">
                        <span class="bi bi-lock-fill"></span>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold">Sign In</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta1/dist/js/adminlte.min.js"></script>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        const formData = new FormData(this);
        
        fetch(this.action, { 
            method: 'POST',
            body: new URLSearchParams(formData).toString(), 
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .then(response => response.json())
        .then(data => {
            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = ''; 
            
            if (data.success) {
                // Login berhasil, redirect
                window.location.href = data.redirect || 'index.php?page=dashboard';
            } else {
                // Tampilkan error dari Controller
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>${data.error}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('alert-container').innerHTML = `
                <div class="alert alert-danger">Terjadi kesalahan koneksi.</div>
            `;
        });
    });
</script>

</body>
</html>