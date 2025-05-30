<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $floors = (int)$_POST['floors'];

    $stmt = $pdo->prepare("INSERT INTO buildings (name, description, floors) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $floors]);

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
                    <h4><i class="bi bi-building-add"></i> Add New Building</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Building Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="floors" class="form-label">Number of Floors</label>
                            <input type="number" class="form-control" id="floors" name="floors" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Building</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>