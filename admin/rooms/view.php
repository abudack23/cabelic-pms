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

// Fetch room details
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

// Fetch active tenants in this room
$stmt = $pdo->prepare("SELECT * FROM tenants WHERE room_id = ? AND status = 'active'");
$stmt->execute([$room_id]);
$tenants = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="bi bi-house-door"></i> Room Details: <?= htmlspecialchars($room['room_number']) ?>
                    </h3>
                    <div>
                        <a href="edit.php?id=<?= $room['id'] ?>" class="btn btn-warning btn-sm me-2">
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
                    <div class="col-md-4 fw-bold">Room Number:</div>
                    <div class="col-md-8"><?= htmlspecialchars($room['room_number']) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 fw-bold">Type:</div>
                    <div class="col-md-8"><?= ucfirst(htmlspecialchars($room['type'])) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 fw-bold">Monthly Rate:</div>
                    <div class="col-md-8">₱<?= number_format($room['rate'], 2) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 fw-bold">Capacity:</div>
                    <div class="col-md-8"><?= htmlspecialchars($room['capacity']) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 fw-bold">Current Tenants:</div>
                    <div class="col-md-8">
                        <?= $room['tenant_count'] ?> / <?= $room['capacity'] ?>
                        <?php if($room['tenant_count'] > 0): ?>
                            <span class="badge bg-<?= $room['tenant_count'] < $room['capacity'] ? 'success' : 'danger' ?> ms-2">
                                <?= $room['tenant_count'] < $room['capacity'] ? 'Available' : 'Full' ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 fw-bold">Status:</div>
                    <div class="col-md-8">
                        <span class="badge bg-<?= 
                            $room['status'] === 'available' ? 'success' : 
                            ($room['status'] === 'occupied' ? 'primary' : 'warning') ?>">
                            <?= ucfirst($room['status']) ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 fw-bold">Description:</div>
                    <div class="col-md-8"><?= htmlspecialchars($room['description']) ?: 'None' ?></div>
                </div>
                
                <?php if(!empty($tenants)): ?>
                    <hr>
                    <h5 class="mb-3"><i class="bi bi-people"></i> Current Tenants</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Move-in Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tenants as $tenant): ?>
                                <tr>
                                    <td>
                                        <a href="../tenants/view.php?id=<?= $tenant['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($tenant['last_name'] . ', ' . $tenant['first_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($tenant['contact_number']) ?></td>
                                    <td><?= date('M d, Y', strtotime($tenant['move_in_date'])) ?></td>
                                    <td>
                                        <a href="../tenants/view.php?id=<?= $tenant['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>