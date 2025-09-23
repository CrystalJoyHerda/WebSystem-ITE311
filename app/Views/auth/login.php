
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
            background: linear-gradient(135deg, #0f2027, #2c5364);
            min-height: 100vh;
        }
        .login-card {
            background: rgba(20, 30, 40, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2.5rem 2rem 2rem 2rem;
            color: #fff;
            position: relative;
        }
        .login-header {
            background: #19d3c5;
            border-radius: 20px 20px 0 0;
            padding: 1.2rem 0.5rem 1rem 0.5rem;
            text-align: center;
            color: #222;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 2px;
        }
        .form-label {
            color: #19d3c5;
            font-weight: 500;
        }
        .form-control {
            background: transparent;
            border: none;
            border-bottom: 2px solid #19d3c5;
            color: #fff;
            border-radius: 0;
            box-shadow: none;
        }
        .form-control:focus {
            background: transparent;
            color: #fff;
            border-color: #19d3c5;
            box-shadow: none;
        }
        .input-group-text {
            background: transparent;
            border: none;
            color: #19d3c5;
            font-size: 1.2rem;
        }
        .btn-login {
            background: #19d3c5;
            color: #222;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.7rem;
            font-size: 1.1rem;
            border: none;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #13b3a7;
            color: #fff;
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
            margin-bottom: 1.2rem;
        }
        .remember-forgot label {
            color: #fff;
        }
        .forgot-link {
            color: #19d3c5;
            text-decoration: none;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        .register-link {
            color: #19d3c5;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-5">
            <div class="login-card">
                <div class="login-header">
                    LOGIN
                </div>
                <div class="pt-3">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success text-center"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger text-center"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?= base_url('login') ?>">
                        <div class="mb-4">
                            <label for="email" class="form-label">Email ID</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                            </div>
                            <?php if (isset($validation) && $validation->hasError('email')): ?>
                                <div class="text-danger mt-1"><?= $validation->getError('email') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <?php if (isset($validation) && $validation->hasError('password')): ?>
                                <div class="text-danger mt-1"><?= $validation->getError('password') ?></div>
                            <?php endif; ?>
                        </div>
                    
                            <a href="#" class="forgot-link">Forgot Password?</a>
                        </div>
                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-login">LOGIN</button>
                        </div>
                        <div class="text-center mt-2">
                            <a href="<?= base_url('register') ?>" class="register-link">Don't have an account? Register</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>