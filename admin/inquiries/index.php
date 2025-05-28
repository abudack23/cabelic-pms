<?php 
require_once '../../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Get all inquiries with tenant information
$stmt = $pdo->query("
    SELECT i.*, 
           t.first_name, t.last_name,
           CASE WHEN i.status = 'pending' THEN 0 ELSE 1 END AS status_order
    FROM inquiries i
    JOIN tenants t ON i.tenant_id = t.id
    ORDER BY status_order, i.created_at DESC
");
$inquiries = $stmt->fetchAll();

// Handle reply submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $inquiryId = $_POST['inquiry_id'];
    $reply = trim($_POST['reply']);
    
    if(empty($reply)) {
        $_SESSION['error'] = "Reply cannot be empty!";
    } else {
        $stmt = $pdo->prepare("UPDATE inquiries SET admin_reply = ?, status = 'answered', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$reply, $inquiryId]);
        
        $_SESSION['success'] = "Reply submitted successfully!";
        header("Location: index.php");
        exit();
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Tenant Inquiries</h1>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Tenant</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($inquiries as $inquiry): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                <td><?php echo $inquiry['first_name'] . ' ' . $inquiry['last_name']; ?></td>
                                <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $inquiry['status'] == 'pending' ? 'warning' : 'success'; ?>">
                                        <?php echo ucfirst($inquiry['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#inquiryModal<?php echo $inquiry['id']; ?>">
                                        View/Reply
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Modal for each inquiry -->
                            <div class="modal fade" id="inquiryModal<?php echo $inquiry['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Inquiry from <?php echo $inquiry['first_name'] . ' ' . $inquiry['last_name']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Submitted:</strong> <?php echo date('M d, Y h:i A', strtotime($inquiry['created_at'])); ?></p>
                                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($inquiry['subject']); ?></p>
                                            <div class="mb-3 p-3 bg-light rounded">
                                                <strong>Tenant's Message:</strong>
                                                <p><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                            </div>
                                            
                                            <?php if($inquiry['status'] == 'answered'): ?>
                                            <div class="mb-3 p-3 bg-info bg-opacity-10 rounded">
                                                <strong>Your Response:</strong>
                                                <p><?php echo nl2br(htmlspecialchars($inquiry['admin_reply'])); ?></p>
                                                <small class="text-muted">Last updated: <?php echo date('M d, Y h:i A', strtotime($inquiry['updated_at'])); ?></small>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <form method="POST">
                                                <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="reply<?php echo $inquiry['id']; ?>" class="form-label">
                                                        <?php echo $inquiry['status'] == 'answered' ? 'Update Response' : 'Your Response'; ?>
                                                    </label>
                                                    <textarea class="form-control" id="reply<?php echo $inquiry['id']; ?>" 
                                                        name="reply" rows="5" required><?php 
                                                        echo $inquiry['status'] == 'answered' ? htmlspecialchars($inquiry['admin_reply']) : ''; 
                                                    ?></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <?php echo $inquiry['status'] == 'answered' ? 'Update Response' : 'Submit Response'; ?>
                                                </button>
                                            </form>
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
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>