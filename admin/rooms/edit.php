<?php
require_once '../../includes/auth_check.php';
if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$room_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if(!$room) {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = $_POST['room_number'];
    $room_type = $_POST['room_type'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE rooms SET 
                          room_number = ?, 
                          room_type = ?, 
                          price = ?, 
                          capacity = ?, 
                          description = ?,
                          status = ?
                          WHERE id = ?");
    $stmt->execute([$room_number, $room_type, $price, $capacity, $description, $status, $room_id]);
    
    $_SESSION['success'] = "Room updated successfully!";
    header("Location: index.php");
    exit();
}
?>

<?php include '../../includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="bi bi-pencil-square"></i> Edit Room</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" class="form-control" name="room_number" 
                               value="<?= htmlspecialchars($room['room_number']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Room Type</label>
                        <select class="form-select" name="room_type" required>
                            <option value="Standard" <?= $room['room_type'] == 'Standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="Deluxe" <?= $room['room_type'] == 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                            <option value="Suite" <?= $room['room_type'] == 'Suite' ? 'selected' : '' ?>>Suite</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Monthly Price (₱)</label>
                        <input type="number" class="form-control" name="price" step="0.01" 
                               value="<?= htmlspecialchars($room['price']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" class="form-control" name="capacity" min="1" 
                               value="<?= htmlspecialchars($room['capacity']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="available" <?= $room['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="occupied" <?= $room['status'] == 'occupied' ? 'selected' : '' ?>>Occupied</option>
                            <option value="maintenance" <?= $room['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($room['description']) ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Room
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>