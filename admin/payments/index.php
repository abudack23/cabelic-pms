<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$status = $_GET['status'] ?? 'all';
$month = $_GET['month'] ?? date('Y-m');

$sql = "SELECT p.*, t.first_name, t.last_name, r.room_number
        FROM payments p
        JOIN rentals l ON p.rental_id = l.id
        JOIN tenants t ON l.tenant_id = t.id
        JOIN rooms r ON l.room_id = r.id";

$conditions = [];
$params = [];

if($status !== 'all') {
    $conditions[] = "p.status = :status";
    $params[':status'] = $status;
}

if(!empty($month)) {
    $conditions[] = "DATE_FORMAT(p.payment_date, '%Y-%m') = :month";
    $params[':month'] = $month;
}

if(!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY p.payment_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

// Get total paid amount
$totalSql = "SELECT SUM(amount) as total FROM payments";
if(!empty($conditions)) {
    $totalSql .= " WHERE " . implode(" AND ", $conditions);
}
$totalStmt = $pdo->prepare($totalSql);
$totalStmt->execute($params);
$totalAmount = $totalStmt->fetchColumn();
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-cash-coin"></i> Payment Management</h1>
            <a href="add.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Record Payment
            </a>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                echo match($_GET['success']) {
                    'added' => 'Payment recorded successfully!',
                    'updated' => 'Payment updated successfully!',
                    'deleted' => 'Payment deleted successfully!',
                    default => 'Operation completed successfully!'
                };
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <input type="month" name="month" class="form-control" value="<?= $month ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="card bg-success text-white w-100">
                            <div class="card-body py-2">
                                <small class="card-title">Total Collected</small>
                                <h5 class="mb-0">₱<?= number_format($totalAmount, 2) ?></h5>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tenant</th>
                                <th>Room</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($payments as $payment): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                <td><?= htmlspecialchars($payment['last_name'] . ', ' . $payment['first_name']) ?></td>
                                <td><?= htmlspecialchars($payment['room_number']) ?></td>
                                <td>₱<?= number_format($payment['amount'], 2) ?></td>
                                <td><?= ucfirst($payment['payment_method']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $payment['status'] === 'paid' ? 'success' : 
                                        ($payment['status'] === 'pending' ? 'warning' : 'danger')
                                    ?>">
                                        <?= ucfirst($payment['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="view.php?id=<?= $payment['id'] ?>" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $payment['id'] ?>" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $payment['id'] ?>" 
                                           class="btn btn-outline-danger" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this payment?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(empty($payments)): ?>
                    <div class="alert alert-info mt-3">No payments found matching your criteria</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>