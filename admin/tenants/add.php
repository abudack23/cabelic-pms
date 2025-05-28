<?php 
require_once '../../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $middleName = $_POST['middle_name'];
    $contact = $_POST['contact_number'];
    $occupation = $_POST['occupation'];
    $residence = $_POST['main_residence'];
    
    $stmt = $pdo->prepare("INSERT INTO tenants (first_name, last_name, middle_name, contact_number, occupation, main_residence) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstName, $lastName, $middleName, $contact, $occupation, $residence]);
    
    // Create user account for tenant
    $tenantId = $pdo->lastInsertId();
    $username = strtolower($firstName . '.' . $lastName);
    $password = password_hash('tenant123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, tenant_id) VALUES (?, ?, 'tenant', ?)");
    $stmt->execute([$username, $password, $tenantId]);
    
    header("Location: index.php?success=1");
    exit();
}
?>

<?php include '../../includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Add New Tenant</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="occupation" class="form-label">Occupation</label>
                        <input type="text" class="form-control" id="occupation" name="occupation" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="main_residence" class="form-label">Main Residence</label>
                        <textarea class="form-control" id="main_residence" name="main_residence" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Tenant</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>