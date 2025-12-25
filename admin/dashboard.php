<?php
session_start();

if (empty($_SESSION['admin_id']) || empty($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Fetch admin info
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #c9d6ff, #e2e2e2);
}

.dashboard {
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.header h2 {
    color: #2f3e55;
}

.logout {
    text-decoration: none;
    background: #100302ff;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    font-weight: 600;
}

.logout:hover {
    background: #0b0201ff;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.card {
    background: #f9fbff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
}

.card h3 {
    margin-bottom: 10px;
    color: #2f3e55;
}

.card a {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #fff;
    background: #7a8dbd;
    padding: 8px 14px;
    border-radius: 6px;
}

.card a:hover {
    background: #5f74a8;
}
</style>
</head>
<body>

<div class="dashboard">
    <div class="header">
        <h2>Welcome, <?= htmlspecialchars($admin['username'] ?? 'Admin'); ?> ! </h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="cards">
        <div class="card">
            <h3>📦 Manage Products</h3>
            <a href="products.php">View</a>
        </div>

        <div class="card">
            <h3>👥 Manage Users</h3>
            <a href="users.php">View</a>
        </div>
    </div>
</div>

</body>
</html>
