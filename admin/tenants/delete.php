<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$tenant_id = (int)$_GET['id'];

// Check if tenant exists
$stmt = $pdo->prepare("SELECT id FROM tenants WHERE id = ?");
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch();

if(!$tenant) {
    header("Location: index.php");
    exit();
}

// Handle deletion
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM tenants WHERE id = ?");
        
        if($stmt->execute([$tenant_id])) {
            header("Location: index.php?success=deleted");
            exit();
        } else {
            throw new Exception('Failed to delete tenant');
        }
    } catch (Exception $e) {
        // Handle error (you might want to log this)
        header("Location: index.php?error=delete_failed");
        exit();
    }
}

// If not POST (direct access), show confirmation
include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h3 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Confirm Deletion</h3>
            </div>
            <div class="card-body">
                <p class="lead">Are you sure you want to delete this tenant?</p>
                <p>This action cannot be undone.</p>
                
                <form method="post">
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Confirm Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>