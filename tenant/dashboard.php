<?php 
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SESSION['role'] !== 'tenant') {
    header("Location: ../login.php");
    exit();
}

// Get tenant information
$stmt = $pdo->prepare("SELECT * FROM tenants WHERE id = ?");
$stmt->execute([$_SESSION['tenant_id']]);
$tenant = $stmt->fetch();

// Get rental information
$stmt = $pdo->prepare("
    SELECT r.*, rm.room_number, rm.price, b.name as building_name 
    FROM rentals r
    JOIN rooms rm ON r.room_id = rm.id
    JOIN buildings b ON rm.building_id = b.id
    WHERE r.tenant_id = ? AND r.status = 'active'
");
$stmt->execute([$_SESSION['tenant_id']]);
$rental = $stmt->fetch();

// Initialize payments array
$payments = [];

// Only fetch payments if rental exists
if ($rental) {
    $stmt = $pdo->prepare("
        SELECT * FROM payments 
        WHERE rental_id = ?
        ORDER BY payment_date DESC
    ");
    $stmt->execute([$rental['id']]);
    $payments = $stmt->fetchAll();
}
?>

<?php include '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Tenant Dashboard</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Your Information</h5>
            </div>
            <div class="card-body">
                <?php if($tenant): ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($tenant['contact_number']); ?></p>
                    <p><strong>Occupation:</strong> <?php echo htmlspecialchars($tenant['occupation']); ?></p>
                    <p><strong>Residence:</strong> <?php echo htmlspecialchars($tenant['main_residence']); ?></p>
                    <a href="profile.php" class="btn btn-sm btn-primary">Edit Profile</a>
                <?php else: ?>
                    <p>No tenant information found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Rental Information</h5>
            </div>
            <div class="card-body">
                <?php if($rental): ?>
                    <p><strong>Building:</strong> <?php echo htmlspecialchars($rental['building_name']); ?></p>
                    <p><strong>Room Number:</strong> <?php echo htmlspecialchars($rental['room_number']); ?></p>
                    <p><strong>Monthly Rent:</strong> ₱<?php echo number_format($rental['price'], 2); ?></p>
                    <p><strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($rental['start_date'])); ?></p>
                    <p><strong>Advance Payment:</strong> ₱<?php echo number_format($rental['advance_payment'], 2); ?></p>
                    <p><strong>Deposit Payment:</strong> ₱<?php echo number_format($rental['deposit_payment'], 2); ?></p>
                <?php else: ?>
                    <p>No active rental found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Payment History</h5>
            </div>
            <div class="card-body">
                <?php if(!empty($payments)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Month Paid For</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($payments as $payment): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                                <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $payment['mode_of_payment'])); ?></td>
                                <td><?php echo date('F Y', strtotime($payment['month_paid_for'])); ?></td>
                                <td><?php echo $payment['notes'] ? htmlspecialchars($payment['notes']) : 'N/A'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="payments.php" class="btn btn-sm btn-primary">View All Payments</a>
                <?php else: ?>
                    <p>No payment history found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>