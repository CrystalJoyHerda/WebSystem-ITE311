
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .register-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e3e3e3;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            max-width: 400px;
            margin: 0 auto;
        }
        .register-header {
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
        .btn-register {
            background: #0d6efd;
            color: #fff;
            font-weight: 600;
            border-radius: 6px;
            padding: 0.7rem;
            font-size: 1.1rem;
            border: none;
            transition: background 0.2s;
        }
        .btn-register:hover {
            background: #0b5ed7;
        }
        .register-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="register-card w-100">
            <div class="register-header">
                Register
            </div>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger text-center"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <form method="post" action="<?= base_url('register') ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('name')): ?>
                        <div class="text-danger mt-1"><?= $validation->getError('name') ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required aria-describedby="emailHelpInline">
                    </div>
                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                        <div class="text-danger mt-1"><?= $validation->getError('email') ?></div>
                    <?php endif; ?>
                    <!-- Real-time validation message (client-side) -->
                    <div id="emailInvalidMsg" class="form-text text-danger small mt-1 d-none">Invalid email format: only letters, numbers, @, and . are allowed.</div>
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
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
                        <div class="text-danger mt-1"><?= $validation->getError('password_confirm') ?></div>
                    <?php endif; ?>
                </div>
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-register">Register</button>
                </div>
                <div class="text-center mt-2">
                    <a href="<?= base_url('login') ?>" class="register-link">Already have an account? Login</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Real-time client-side email character validation
        (function(){
            var emailInput = document.getElementById('email');
            var emailMsg = document.getElementById('emailInvalidMsg');
            var form = document.querySelector('form[action="<?= base_url('register') ?>"]');

            // Allowed characters: letters, numbers, @ and .
            var allowedRe = /^[A-Za-z0-9@.]*$/; // allow empty while typing

            function checkEmailChars() {
                var v = emailInput.value || '';
                // strip leading/trailing spaces for validation
                var trimmed = v.trim();
                if (!allowedRe.test(trimmed)) {
                    emailMsg.classList.remove('d-none');
                    return false;
                }
                emailMsg.classList.add('d-none');
                return true;
            }

            // Validate while typing
            emailInput.addEventListener('input', function(){
                checkEmailChars();
            });

            // On form submit, enforce the character rule (prevent submit if invalid)
            if (form) {
                form.addEventListener('submit', function(e){
                    if (!checkEmailChars()) {
                        // prevent submission and focus the field
                        e.preventDefault();
                        e.stopPropagation();
                        emailInput.focus();
                    }
                });
            }
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>