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

$stmt = $pdo->prepare("SELECT * FROM tenants WHERE id = ?");
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch();

if(!$tenant) {
    header("Location: index.php");
    exit();
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="bi bi-person-badge"></i> Tenant Details
                    </h3>
                    <div>
                        <a href="edit.php?id=<?= $tenant['id'] ?>" class="btn btn-warning btn-sm me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="index.php" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">ID:</div>
                    <div class="col-md-9"><?= htmlspecialchars($tenant['id']) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Full Name:</div>
                    <div class="col-md-9">
                        <?= htmlspecialchars($tenant['last_name'] . ', ' . $tenant['first_name'] . ' ' . ($tenant['middle_name'] ?? '')) ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Contact Number:</div>
                    <div class="col-md-9"><?= htmlspecialchars($tenant['contact_number']) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Occupation:</div>
                    <div class="col-md-9"><?= htmlspecialchars($tenant['occupation']) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Status:</div>
                    <div class="col-md-9">
                        <span class="badge bg-<?= $tenant['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($tenant['status']) ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Created At:</div>
                    <div class="col-md-9"><?= date('M d, Y H:i', strtotime($tenant['created_at'])) ?></div>
                </div>
                <?php if(!empty($tenant['updated_at'])): ?>
                <div class="row">
                    <div class="col-md-3 fw-bold">Last Updated:</div>
                    <div class="col-md-9"><?= date('M d, Y H:i', strtotime($tenant['updated_at'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>