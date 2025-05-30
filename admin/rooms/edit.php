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

$room_id = (int)$_GET['id'];
$error = '';
$room_types = ['standard', 'deluxe', 'suite', 'executive'];

// Fetch existing room data
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if(!$room) {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'id' => $room_id,
            'room_number' => trim($_POST['room_number']),
            'type' => $_POST['type'],
            'rate' => (float)$_POST['rate'],
            'capacity' => (int)$_POST['capacity'],
            'status' => $_POST['status'],
            'description' => trim($_POST['description'] ?? '')
        ];

        // Validation
        if(empty($data['room_number'])) {
            throw new Exception('Room number is required');
        }
        if(!in_array($data['type'], $room_types)) {
            throw new Exception('Invalid room type');
        }
        if($data['rate'] <= 0) {
            throw new Exception('Rate must be greater than 0');
        }

        $sql = "UPDATE rooms SET 
                room_number = :room_number,
                type = :type,
                rate = :rate,
                capacity = :capacity,
                status = :status,
                description = :description
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        
        if($stmt->execute($data)) {
            header("Location: index.php?success=updated");
            exit();
        } else {
            throw new Exception('Failed to update room');
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
                <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Room</h3>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="room_number" class="form-label">Room Number *</label>
                            <input type="text" class="form-control" id="room_number" name="room_number" 
                                   value="<?= htmlspecialchars($room['room_number']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">Room Type *</label>
                            <select class="form-select" id="type" name="type" required>
                                <?php foreach($room_types as $type): ?>
                                    <option value="<?= $type ?>" <?= $room['type'] === $type ? 'selected' : '' ?>>
                                        <?= ucfirst($type) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="rate" class="form-label">Monthly Rate (₱) *</label>
                            <input type="number" step="0.01" class="form-control" id="rate" name="rate" 
                                   value="<?= htmlspecialchars($room['rate']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="capacity" class="form-label">Capacity *</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   value="<?= htmlspecialchars($room['capacity']) ?>" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" <?= $room['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="occupied" <?= $room['status'] === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                                <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($room['description']) ?></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-save"></i> Update Room
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