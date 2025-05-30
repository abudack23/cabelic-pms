<?php
require_once '../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                                <i class="bi bi-house"></i> General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button">
                                <i class="bi bi-cash-coin"></i> Payments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button">
                                <i class="bi bi-bell"></i> Notifications
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="settingsTabsContent">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Property Name</label>
                                <input type="text" class="form-control" name="property_name" value="Cabelic Apartments">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="property_address" rows="3">123 Main Street, City</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="contact_email" value="info@cabelicapartment.com">
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div class="tab-pane fade" id="payment" role="tabpanel">
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
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <select class="form-select" name="due_date">
                                    <?php for($i = 1; $i <= 28; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == 5 ? 'selected' : '' ?>>
                                            <?= $i ?><?= $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) ?> of the month
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                                    <label class="form-check-label" for="email_notifications">Enable Email Notifications</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications">
                                    <label class="form-check-label" for="sms_notifications">Enable SMS Notifications</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notification Email</label>
                                <input type="email" class="form-control" name="notification_email" value="notifications@cabelicapartment.com">
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