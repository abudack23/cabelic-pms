<?php 
require_once '../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Admin Dashboard</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Rooms</h5>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM rooms");
                $totalRooms = $stmt->fetchColumn();
                ?>
                <h2><?php echo $totalRooms; ?></h2>
                <a href="rooms/index.php" class="text-white">View Rooms</a>
            </div>
        </div>
    </div>
    
    <!-- Occupied Rooms Card - Corrected link -->
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Occupied Rooms</h5>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status = 'occupied'");
                $occupiedRooms = $stmt->fetchColumn();
                ?>
                <h2><?php echo $occupiedRooms; ?></h2>
                <a href="rooms/index.php?status=occupied" class="text-white">View Occupied</a>
            </div>
        </div>
    </div>
    
    <!-- Active Tenants Card - Unchanged -->
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Active Tenants</h5>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM tenants");
                $totalTenants = $stmt->fetchColumn();
                ?>
                <h2><?php echo $totalTenants; ?></h2>
                <a href="tenants/index.php" class="text-white">View Tenants</a>
            </div>
        </div>
    </div>
    <!-- Pending Payments Card - Unchanged -->
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Pending Payments</h5>
                <?php
                $pendingPayments = 0;
                ?>
                <h2><?php echo $pendingPayments; ?></h2>
                <a href="payments/index.php" class="text-dark">View Payments</a>
            </div>
        </div>
    </div>
</div>

<!-- Rest of your dashboard remains EXACTLY the same -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Payments</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT p.amount, p.payment_date, t.first_name, t.last_name 
                            FROM payments p
                            JOIN rentals r ON p.rental_id = r.id
                            JOIN tenants t ON r.tenant_id = t.id
                            ORDER BY p.payment_date DESC LIMIT 5
                        ");
                        while($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                            <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['payment_date'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="payments/index.php" class="btn btn-sm btn-primary">View All Payments</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Tenants</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Occupation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM tenants ORDER BY created_at DESC LIMIT 5");
                        while($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                            <td><?php echo $row['contact_number']; ?></td>
                            <td><?php echo $row['occupation']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="tenants/index.php" class="btn btn-sm btn-primary">View All Tenants</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links Section - Corrected Add Room link -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <a href="tenants/add.php" class="btn btn-outline-primary">Add New Tenant</a>
                    <a href="rooms/add.php" class="btn btn-outline-primary">Add New Room</a>
                    <a href="payments/add.php" class="btn btn-outline-primary">Record Payment</a>
                    <a href="inquiries/" class="btn btn-outline-primary">View Inquiries</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>