<?php 
require_once '../../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Get all tenants
$stmt = $pdo->query("SELECT * FROM tenants ORDER BY last_name, first_name");
$tenants = $stmt->fetchAll();
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Tenant Management</h1>
        <a href="add.php" class="btn btn-primary mb-3">Add New Tenant</a>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Tenant added successfully!</div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Occupation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tenants as $tenant): ?>
                        <tr>
                            <td><?php echo $tenant['id']; ?></td>
                            <td><?php echo $tenant['last_name'] . ', ' . $tenant['first_name'] . ' ' . $tenant['middle_name']; ?></td>
                            <td><?php echo $tenant['contact_number']; ?></td>
                            <td><?php echo $tenant['occupation']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $tenant['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?php echo $tenant['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>