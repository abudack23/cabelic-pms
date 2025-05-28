<?php 
require_once '../includes/auth_check.php';
if($_SESSION['role'] !== 'tenant') {
    header("Location: ../login.php");
    exit();
}

// Get tenant information
$stmt = $pdo->prepare("SELECT * FROM tenants WHERE id = ?");
$stmt->execute([$_SESSION['tenant_id']]);
$tenant = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $middleName = $_POST['middle_name'];
    $contact = $_POST['contact_number'];
    $occupation = $_POST['occupation'];
    $residence = $_POST['main_residence'];
    
    $stmt = $pdo->prepare("UPDATE tenants SET first_name = ?, last_name = ?, middle_name = ?, contact_number = ?, occupation = ?, main_residence = ? WHERE id = ?");
    $stmt->execute([$firstName, $lastName, $middleName, $contact, $occupation, $residence, $_SESSION['tenant_id']]);
    
    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: profile.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>My Profile</h4>
            </div>
            <div class="card-body">
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $tenant['first_name']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $tenant['last_name']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo $tenant['middle_name']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo $tenant['contact_number']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="occupation" class="form-label">Occupation</label>
                        <input type="text" class="form-control" id="occupation" name="occupation" value="<?php echo $tenant['occupation']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="main_residence" class="form-label">Main Residence</label>
                        <textarea class="form-control" id="main_residence" name="main_residence" rows="3"><?php echo $tenant['main_residence']; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
                
                <hr>
                
                <h5 class="mt-4">Change Password</h5>
                <form method="POST" action="change_password.php">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>