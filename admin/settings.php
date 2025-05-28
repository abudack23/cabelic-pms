<?php
require_once '../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process settings update here
    $_SESSION['success'] = "Settings updated successfully";
    header("Location: settings.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-gear"></i> System Settings</h1>
        </div>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="bi bi-building"></i> Property Information</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Property Name</label>
                                <input type="text" class="form-control" name="property_name" value="Cabelic Apartments">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="property_address" rows="3">123 Main Street, City</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="bi bi-currency-dollar"></i> Payment Settings</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <select class="form-select" name="currency">
                                    <option value="PHP" selected>Philippine Peso (₱)</option>
                                    <option value="USD">US Dollar ($)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Late Payment Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="late_fee" value="500">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3"><i class="bi bi-bell"></i> Notification Settings</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                                <label class="form-check-label" for="email_notifications">Email Notifications</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sms_notifications">
                                <label class="form-check-label" for="sms_notifications">SMS Notifications</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>