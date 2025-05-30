<?php
require_once '../../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$paymentId = $_GET['id'];
$stmt = $pdo->prepare("
    SELECT p.*, t.first_name, t.last_name, t.contact_number, r.room_number
    FROM payments p
    JOIN tenants t ON p.tenant_id = t.id
    LEFT JOIN rentals l ON p.rental_id = l.id
    LEFT JOIN rooms r ON l.room_id = r.id
    WHERE p.id = ?
");
$stmt->execute([$paymentId]);
$payment = $stmt->fetch();

if(!$payment) {
    header("Location: index.php");
    exit();
}
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-receipt"></i> Payment Details</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Payments
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Payment ID</dt>
                    <dd class="col-sm-8"><?= $payment['id'] ?></dd>

                    <dt class="col-sm-4">Tenant</dt>
                    <dd class="col-sm-8">
                        <?= htmlspecialchars($payment['first_name'].' '.$payment['last_name']) ?>
                        (<?= htmlspecialchars($payment['contact_number']) ?>)
                    </dd>

                    <dt class="col-sm-4">Room Number</dt>
                    <dd class="col-sm-8"><?= $payment['room_number'] ?? 'N/A' ?></dd>

                    <dt class="col-sm-4">Amount</dt>
                    <dd class="col-sm-8">₱<?= number_format($payment['amount'], 2) ?></dd>

                    <dt class="col-sm-4">Payment Date</dt>
                    <dd class="col-sm-8"><?= date('F j, Y', strtotime($payment['payment_date'])) ?></dd>

                    <dt class="col-sm-4">Payment Method</dt>
                    <dd class="col-sm-8"><?= ucfirst($payment['payment_method']) ?></dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= $payment['status'] === 'paid' ? 'success' : 'warning' ?>">
                            <?= ucfirst($payment['status']) ?>
                        </span>
                    </dd>

                    <dt class="col-sm-4">Notes</dt>
                    <dd class="col-sm-8"><?= $payment['notes'] ? htmlspecialchars($payment['notes']) : 'N/A' ?></dd>
                </dl>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="edit.php?id=<?= $payment['id'] ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="receipt.php?id=<?= $payment['id'] ?>" class="btn btn-primary" target="_blank">
                        <i class="bi bi-printer"></i> Print Receipt
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>