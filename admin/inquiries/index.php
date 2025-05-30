<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$status = $_GET['status'] ?? 'all';
$sql = "SELECT * FROM inquiries";

if($status !== 'all') {
    $sql .= " WHERE status = :status";
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);

if($status !== 'all') {
    $stmt->bindParam(':status', $status);
}

$stmt->execute();
$inquiries = $stmt->fetchAll();
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-question-circle"></i> Inquiry Management</h1>
            <a href="add.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Inquiry
            </a>
        </div>

        <!-- Status Filter Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $status === 'all' ? 'active' : '' ?>" href="index.php">All Inquiries</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'new' ? 'active' : '' ?>" href="index.php?status=new">New</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" href="index.php?status=pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status === 'resolved' ? 'active' : '' ?>" href="index.php?status=resolved">Resolved</a>
            </li>
        </ul>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                echo match($_GET['success']) {
                    'added' => 'Inquiry added successfully!',
                    'updated' => 'Inquiry updated successfully!',
                    'deleted' => 'Inquiry deleted successfully!',
                    default => 'Operation completed successfully!'
                };
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($inquiries as $inquiry): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($inquiry['created_at'])) ?></td>
                                <td><?= htmlspecialchars($inquiry['name']) ?></td>
                                <td>
                                    <?= htmlspecialchars($inquiry['email']) ?><br>
                                    <?= htmlspecialchars($inquiry['phone']) ?>
                                </td>
                                <td><?= htmlspecialchars($inquiry['subject']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $inquiry['status'] === 'resolved' ? 'success' : 
                                        ($inquiry['status'] === 'pending' ? 'warning' : 'info')
                                    ?>">
                                        <?= ucfirst($inquiry['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="view.php?id=<?= $inquiry['id'] ?>" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $inquiry['id'] ?>" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $inquiry['id'] ?>" 
                                           class="btn btn-outline-danger" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this inquiry?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(empty($inquiries)): ?>
                    <div class="alert alert-info mt-3">No inquiries found</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>