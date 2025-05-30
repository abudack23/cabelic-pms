<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $room_number = $_POST['room_number'];
    $type = $_POST['type'];
    $rate = $_POST['rate'];
    $capacity = $_POST['capacity'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO rooms (room_number, type, rate, capacity, status, description) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$room_number, $type, $rate, $capacity, $status, $description]);

    header("Location: index.php?success=added");
    exit();
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4><i class="bi bi-house-add"></i> Add New Room</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="room_number" class="form-label">Room Number</label>
                            <input type="text" class="form-control" id="room_number" name="room_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Room Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="standard">Standard</option>
                                <option value="deluxe">Deluxe</option>
                                <option value="suite">Suite</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rate" class="form-label">Monthly Rate (₱)</label>
                            <input type="number" class="form-control" id="rate" name="rate" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Room</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>