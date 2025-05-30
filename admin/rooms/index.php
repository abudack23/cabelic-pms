<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/db_connect.php';

if($_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

// CORRECTED SQL QUERY - using subquery instead of join
$sql = "SELECT r.*, 
        (SELECT COUNT(*) FROM rentals WHERE room_id = r.id AND status = 'active') as tenant_count 
        FROM rooms r";

$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(r.room_number LIKE :search OR r.type LIKE :search OR r.description LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($status_filter !== 'all') {
    $conditions[] = "r.status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY r.room_number";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rooms = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>