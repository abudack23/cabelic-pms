<?php
// Enable full error reporting at the top
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
echo "<!-- Session Status: " . (empty($_SESSION) ? 'Empty' : 'Active') . " -->";
// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'tenant/dashboard.php'));
    exit();
}

require_once 'config/database.php';

// Debug database connection
try {
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle success messages
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Login processing
$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Enhanced input validation
    if(empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            // Case-sensitive username check
            $stmt = $pdo->prepare("SELECT * FROM users WHERE BINARY username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            // Debug output (comment this out after testing)
            echo "<div class='container mt-3 debug-output' style='background:#f8f9fa;padding:20px;border-radius:5px;'>";
            echo "<h5>Debug Information:</h5>";
            echo "<pre>User Query Results: "; print_r($user); echo "</pre>";
            
            if($user) {
                echo "<p>Input Password: " . htmlspecialchars($password) . "</p>";
                echo "<p>Stored Hash: " . htmlspecialchars($user['password']) . "</p>";
                
                // Check if password needs rehashing
                if(password_verify($password, $user['password'])) {
                    if(password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                            ->execute([$newHash, $user['id']]);
                    }
                    
                    // Session regeneration for security
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['tenant_id'] = $user['tenant_id'] ?? null;
                    
                    echo "<p class='text-success'>Password verification successful! Redirecting...</p>";
                    echo "</div>"; // Close debug output
                    
                    // Redirect to appropriate dashboard
                    header("Location: " . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'tenant/dashboard.php'));
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User not found";
            }
            echo "</div>"; // Close debug output
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!-- Rest of your HTML remains the same -->
<?php include 'includes/header.php'; ?>

<div class="row justify-content-center min-vh-100">
    <div class="col-md-6 my-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Login</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person-fill"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        <div class="invalid-feedback">
                            Please enter your username
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock-fill"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">
                            Please enter your password
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </div>
                </form>
                
                <div class="text-center">
                    <a href="forgot-password.php" class="text-muted">Forgot password?</a>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Home
                    </a>
                    <a href="register.php" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap form validation
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
            }, false);
        });
})();
</script>

<?php include 'includes/footer.php'; ?>