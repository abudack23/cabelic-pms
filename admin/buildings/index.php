<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

$stmt = $pdo->query("SELECT * FROM buildings ORDER BY name");
$buildings = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-buildings"></i> Building Management</h1>
        <a href="add.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Building
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>ID</th>
                            <th>Building Name</th>
                            <th>Description</th>
                            <th>Floors</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($buildings as $building): ?>
                        <tr>
                            <td>
                                <div class="btn-group">
                                    <a href="edit.php?id=<?= $building['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="copy.php?id=<?= $building['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-files"></i> Copy
                                    </a>
                                    <a href="delete.php?id=<?= $building['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this building?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                            <td><?= $building['id'] ?></td>
                            <td><?= htmlspecialchars($building['name']) ?></td>
                            <td><?= htmlspecialchars($building['description']) ?></td>
                            <td><?= $building['floors'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>