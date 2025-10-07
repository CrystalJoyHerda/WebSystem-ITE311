
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e3e3e3;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            max-width: 400px;
            margin: 0 auto;
        }
        .login-header {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
            color: #222;
            letter-spacing: 1px;
        }
        .form-label {
            font-weight: 500;
            color: #222;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            background: #fff;
            color: #222;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: none;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 6px 0 0 6px;
            color: #888;
        }
        .btn-login {
            background: #0d6efd;
            color: #fff;
            font-weight: 600;
            border-radius: 6px;
            padding: 0.7rem;
            font-size: 1.1rem;
            border: none;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #0b5ed7;
        }
        .forgot-link, .register-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover, .register-link:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="login-card w-100">
            <div class="login-header">
                Login
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger text-center"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <form method="post" action="<?= base_url('login') ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                        <div class="text-danger mt-1"><?= $validation->getError('email') ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('password')): ?>
                        <div class="text-danger mt-1"><?= $validation->getError('password') ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-login">Login</button>
                </div>
                <div class="text-center mt-2">
                    <a href="<?= base_url('register') ?>" class="register-link">Don't have an account? Register</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>