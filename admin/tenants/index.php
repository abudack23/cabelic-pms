<?php 
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM tenants";
$conditions = [];
$params = [];

if ($status_filter !== 'all') {
    $conditions[] = "status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($search)) {
    $conditions[] = "(first_name LIKE :search OR last_name LIKE :search OR contact_number LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY last_name, first_name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tenants = $stmt->fetchAll();
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<style>
.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-view {
    background-color: #0d6efd;
    color: white;
}

.btn-view:hover {
    background-color: #0b5ed7;
    color: white;
}

.btn-edit {
    background-color: #fd7e14;
    color: white;
}

.btn-edit:hover {
    background-color: #e55a00;
    color: white;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-delete:hover {
    background-color: #bb2d3b;
    color: white;
}

.add-btn {
    background-color: #198754;
    border-color: #198754;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.add-btn:hover {
    background-color: #157347;
    border-color: #146c43;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.status-badge {
    font-size: 0.875rem;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 500;
}

.search-form .form-control, .search-form .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.search-form .form-control:focus, .search-form .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.search-form .btn {
    border-radius: 8px;
    transition: all 0.2s ease;
}

.search-form .btn:hover {
    transform: translateY(-1px);
}

.card {
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table {
    border-radius: 8px;
    overflow: hidden;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-people"></i> Tenant Management</h1>
            <a href="add.php" class="btn btn-success add-btn">
                <i class="bi bi-person-plus"></i> Add New Tenant
            </a>
        </div>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                echo match($_GET['success']) {
                    'added' => 'Tenant added successfully!',
                    'updated' => 'Tenant updated successfully!',
                    'deleted' => 'Tenant deleted successfully!',
                    default => 'Operation completed successfully!'
                };
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3 search-form">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search tenants..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Occupation</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tenants as $tenant): ?>
                            <tr>
                                <td><?= htmlspecialchars($tenant['id']) ?></td>
                                <td>
                                    <a href="view.php?id=<?= $tenant['id'] ?>" class="text-decoration-none fw-medium">
                                        <?= htmlspecialchars($tenant['last_name'] . ', ' . $tenant['first_name'] . ' ' . ($tenant['middle_name'] ? $tenant['middle_name'] : '')) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($tenant['contact_number']) ?></td>
                                <td><?= htmlspecialchars($tenant['occupation']) ?></td>
                                <td>
                                    <span class="badge status-badge bg-<?= $tenant['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($tenant['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex">
                                        <a href="view.php?id=<?= $tenant['id'] ?>" class="action-btn btn-view" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit.php?id=<?= $tenant['id'] ?>" class="action-btn btn-edit" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $tenant['id'] ?>" 
                                           class="action-btn btn-delete" 
                                           title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this tenant?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if(empty($tenants)): ?>
                    <div class="alert alert-info text-center mt-3">
                        <i class="bi bi-info-circle"></i> No tenants found matching your criteria.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>