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
$error = '';
$success = '';

// Fetch existing tenant data
$stmt = $pdo->prepare("SELECT * FROM tenants WHERE id = ?");
$stmt->execute([$tenant_id]);
$tenant = $stmt->fetch();

if(!$tenant) {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'id' => $tenant_id,
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'middle_name' => trim($_POST['middle_name'] ?? ''),
            'contact_number' => trim($_POST['contact_number']),
            'occupation' => trim($_POST['occupation']),
            'status' => $_POST['status'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Basic validation
        if(empty($data['first_name']) || empty($data['last_name'])) {
            throw new Exception('First name and last name are required');
        }

        $sql = "UPDATE tenants SET 
                first_name = :first_name,
                last_name = :last_name,
                middle_name = :middle_name,
                contact_number = :contact_number,
                occupation = :occupation,
                status = :status,
                updated_at = :updated_at
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        
        if($stmt->execute($data)) {
            header("Location: index.php?success=updated");
            exit();
        } else {
            throw new Exception('Failed to update tenant');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Tenant</h3>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?= htmlspecialchars($tenant['first_name']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?= htmlspecialchars($tenant['last_name']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" 
                                   value="<?= htmlspecialchars($tenant['middle_name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                   value="<?= htmlspecialchars($tenant['contact_number']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" class="form-control" id="occupation" name="occupation" 
                                   value="<?= htmlspecialchars($tenant['occupation']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?= $tenant['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $tenant['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-save"></i> Update Tenant
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>