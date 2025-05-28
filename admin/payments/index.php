<?php 
require_once '../../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Get all payments with tenant and room info
$stmt = $pdo->query("
    SELECT p.*, 
           t.first_name, t.last_name,
           rm.room_number,
           b.name as building_name
    FROM payments p
    JOIN rentals r ON p.rental_id = r.id
    JOIN tenants t ON r.tenant_id = t.id
    JOIN rooms rm ON r.room_id = rm.id
    JOIN buildings b ON rm.building_id = b.id
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll();
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Payment Records</h1>
        <a href="add.php" class="btn btn-primary mb-3">Record New Payment</a>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Tenant</th>
                            <th>Room</th>
                            <th>Amount</th>
                            <th>For Month</th>
                            <th>Method</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($payments as $payment): ?>
                        <tr>
                            <td><?php echo date('m/d/Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?></td>
                            <td><?php echo $payment['building_name'] . ' - ' . $payment['room_number']; ?></td>
                            <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo date('F Y', strtotime($payment['month_paid_for'])); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment['mode_of_payment'])); ?></td>
                            <td><?php echo $payment['notes'] ?: 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>