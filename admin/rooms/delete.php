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

$room_id = (int)$_GET['id'];

// Check if room exists and has no active tenants
$stmt = $pdo->prepare("SELECT r.*, COUNT(t.id) as tenant_count 
                       FROM rooms r
                       LEFT JOIN tenants t ON t.room_id = r.id AND t.status = 'active'
                       WHERE r.id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if(!$room) {
    header("Location: index.php");
    exit();
}

// Handle deletion
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if room has active tenants
        if($room['tenant_count'] > 0) {
            throw new Exception('Cannot delete room with active tenants');
        }

        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
        
        if($stmt->execute([$room_id])) {
            header("Location: index.php?success=deleted");
            exit();
        } else {
            throw new Exception('Failed to delete room');
        }
    } catch (Exception $e) {
        $_SESSION['flash_message'] = $e->getMessage();
        $_SESSION['flash_message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h3 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Confirm Deletion</h3>
            </div>
            <div class="card-body">
                <?php if($room['tenant_count'] > 0): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-octagon"></i> This room has <?= $room['tenant_count'] ?> active tenant(s) and cannot be deleted.
                    </div>
                <?php else: ?>
                    <p class="lead">Are you sure you want to delete Room <?= htmlspecialchars($room['room_number']) ?>?</p>
                    <p>This action cannot be undone.</p>
                <?php endif; ?>
                
                <form method="post">
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <?php if($room['tenant_count'] === 0): ?>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Confirm Delete
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>