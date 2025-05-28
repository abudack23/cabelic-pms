<?php
session_start();

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'tenant/dashboard.php'));
    exit();
}

require 'config/database.php';

$errors = [];
$formData = [
    'full_name' => '',
    'email' => '',
    'username' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and store form data
    $formData = [
        'full_name' => trim($_POST['full_name']),
        'email' => trim($_POST['email']),
        'username' => trim($_POST['username'])
    ];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = 'tenant'; // Default role for registrations

    // Validation
    if (empty($formData['full_name'])) $errors[] = "Full name is required";
    if (empty($formData['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($formData['username'])) {
        $errors[] = "Username is required";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $formData['username'])) {
        $errors[] = "Username can only contain letters, numbers and underscores";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number and one uppercase letter";
    }
    if ($password !== $password_confirm) $errors[] = "Passwords don't match";

    // Check if username/email exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$formData['username'], $formData['email']]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Create tenant first
            $name_parts = explode(' ', $formData['full_name'], 2);
            $stmt = $pdo->prepare("INSERT INTO tenants (first_name, last_name, contact_number) VALUES (?, ?, '')");
            $stmt->execute([$name_parts[0], $name_parts[1] ?? '']);
            $tenant_id = $pdo->lastInsertId();

            // Create user account
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, full_name, password, role, tenant_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $formData['username'],
                $formData['email'],
                $formData['full_name'],
                $hashed_password,
                $role,
                $tenant_id
            ]);

            $pdo->commit();
            
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Registration failed. Please try again.";
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center min-vh-100">
    <div class="col-md-8 col-lg-6 my-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Register New Account</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Please fix these errors:</h5>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            <i class="bi bi-person-badge"></i> Full Name
                        </label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($formData['full_name']) ?>" required>
                        <div class="invalid-feedback">
                            Please enter your full name
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> Email Address
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($formData['email']) ?>" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person-circle"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= htmlspecialchars($formData['username']) ?>" required
                               pattern="[a-zA-Z0-9_]+" title="Letters, numbers and underscores only">
                        <div class="invalid-feedback">
                            Please enter a valid username (letters, numbers, underscores)
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Password (min 8 characters)
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required
                               minlength="8" pattern="^(?=.*[A-Z])(?=.*\d).+$"
                               title="At least 8 characters with 1 number and 1 uppercase letter">
                        <div class="invalid-feedback">
                            Password must be 8+ characters with at least 1 number and 1 uppercase letter
                        </div>
                        <div class="form-text">
                            Must contain at least one number and one uppercase letter
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">
                            <i class="bi bi-lock-fill"></i> Confirm Password
                        </label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        <div class="invalid-feedback">
                            Passwords must match
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Home
                        </a>
                        <a href="login.php" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Existing Account? Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side form validation
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms)
        .forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
                
                // Custom password match validation
                const password = document.getElementById('password');
                const confirm = document.getElementById('password_confirm');
                if (password.value !== confirm.value) {
                    confirm.setCustomValidity("Passwords must match");
                } else {
                    confirm.setCustomValidity('');
                }
            }, false);
        });
})();
</script>

<?php include 'includes/footer.php'; ?>