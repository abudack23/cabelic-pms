<?php 
require_once '../includes/auth_check.php';
if($_SESSION['role'] !== 'tenant') {
    header("Location: ../login.php");
    exit();
}

// Get tenant's inquiries with sorting by status and date
$stmt = $pdo->prepare("
    SELECT *, 
           CASE WHEN status = 'pending' THEN 0 ELSE 1 END AS status_order
    FROM inquiries 
    WHERE tenant_id = ? 
    ORDER BY status_order, created_at DESC
");
$stmt->execute([$_SESSION['tenant_id']]);
$inquiries = $stmt->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validate input
    if(empty($subject) || empty($message)) {
        $_SESSION['error'] = "Subject and message are required!";
    } elseif(strlen($subject) > 255) {
        $_SESSION['error'] = "Subject is too long!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO inquiries (tenant_id, subject, message) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['tenant_id'], $subject, $message]);
        
        $_SESSION['success'] = "Inquiry submitted successfully!";
        header("Location: inquiries.php");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <h1 class="mb-4">Inquiries</h1>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Submit New Inquiry</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Inquiry</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">My Inquiry History</h5>
            </div>
            <div class="card-body">
                <?php if(count($inquiries) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($inquiries as $inquiry): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $inquiry['status'] == 'pending' ? 'warning' : 'success'; ?>">
                                            <?php echo ucfirst($inquiry['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#inquiryModal<?php echo $inquiry['id']; ?>">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Modal for each inquiry -->
                                <div class="modal fade" id="inquiryModal<?php echo $inquiry['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Inquiry Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($inquiry['created_at'])); ?></p>
                                                <p><strong>Subject:</strong> <?php echo htmlspecialchars($inquiry['subject']); ?></p>
                                                <div class="mb-3 p-3 bg-light rounded">
                                                    <strong>Your Message:</strong>
                                                    <p><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                                </div>
                                                
                                                <?php if($inquiry['status'] == 'answered'): ?>
                                                <div class="p-3 bg-info bg-opacity-10 rounded">
                                                    <strong>Admin Response:</strong>
                                                    <p><?php echo nl2br(htmlspecialchars($inquiry['admin_reply'])); ?></p>
                                                    <small class="text-muted">Last updated: <?php echo date('M d, Y h:i A', strtotime($inquiry['updated_at'])); ?></small>
                                                </div>
                                                <?php else: ?>
                                                <div class="alert alert-warning">
                                                    Your inquiry is pending. We'll respond soon.
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        You haven't submitted any inquiries yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>