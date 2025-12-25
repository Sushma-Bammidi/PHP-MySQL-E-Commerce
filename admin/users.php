<?php
session_start();
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

/* ---------- ADD USER ---------- */
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->execute([$email]);

    if ($check->rowCount() == 0) {
        $stmt = $conn->prepare(
            "INSERT INTO users (username,email,password,role,created_at) VALUES (?,?,?,?,NOW())"
        );
        $stmt->execute([$username,$email,$password,$role]);
    }
    header("Location: users.php");
    exit();
}

/* ---------- DELETE USER ---------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role!='admin'");
    $stmt->execute([$id]);
    header("Location: users.php");
    exit();
}

/* ---------- UPDATE ROLE ---------- */
if (isset($_POST['update_role'])) {
    $id = $_POST['id'];
    $role = $_POST['role'];
    $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->execute([$role,$id]);
    header("Location: users.php");
    exit();
}

/* ---------- SEARCH ---------- */
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%","%$search%"]);
} else {
    $stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
<style>
body{font-family:Segoe UI;background:#e9eef8;margin:0;}
.container{max-width:1100px;margin:30px auto;background:#fff;padding:30px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.15);}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
h2{color:#2f3e55;margin:0;}
.back{background:#3498db;color:#fff;padding:8px 14px;border-radius:6px;text-decoration:none;}
.back:hover{background:#2980b9;}

.add-box{background:#f9fbff;padding:15px;border-radius:12px;margin-bottom:20px;}
.add-box form{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;}
.add-box input,.add-box select{padding:8px;border-radius:6px;border:1px solid #ccc;}
.add-box button{grid-column:1/-1;padding:10px;border:none;border-radius:6px;background:#28a745;color:#fff;font-weight:600;}
.add-box button{
    grid-column:1/-1;
    padding:10px;
    border:none;
    border-radius:6px;
    background: #397a45ff;;
    color:#fff;
    font-weight:600;
    cursor:pointer;
}

.add-box button:hover{
    background: #2e5d36ff;
}


.search{margin-bottom:15px;}
.search input{padding:8px;border-radius:6px;border:1px solid #ccc;width:250px;}

table{width:100%;border-collapse:collapse;}
th,td{padding:12px;border-bottom:1px solid #ddd;text-align:center;}
th{background:#f2f4f8;color:#2f3e55;}
select,button{padding:6px;border-radius:5px;}
button{border:none;background:#7a8dbd;color:#fff;cursor:pointer;}
button:hover{background:#5f74a8;}
.del{background:#e74c3c;padding:6px 10px;border-radius:5px;color:#fff;text-decoration:none;}
.del:hover{background:#c0392b;}
.del{
    background: #000;   /* black */
    color: #fff;
}

.del:hover{
    background: #333;   /* dark gray on hover */
}


</style>
</head>
<body>

<div class="container">
<div class="top">
<h2>👥 Manage Users</h2>
<a href="dashboard.php" class="back">← Dashboard</a>
</div>

<!-- ADD USER -->
<div class="add-box">
<h3>➕ Add New User</h3>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<select name="role">
<option value="user">User</option>
<option value="admin">Admin</option>
</select>
<button name="add_user">Add User</button>
</form>
</div>

<!-- SEARCH -->
<div class="search">
<form>
<input type="text" name="search" placeholder="Search user..." value="<?= htmlspecialchars($search) ?>">
</form>
</div>

<table>
<tr>
<th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td>
<form method="POST">
<input type="hidden" name="id" value="<?= $u['id'] ?>">
<select name="role">
<option value="user" <?= $u['role']=='user'?'selected':'' ?>>User</option>
<option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
</select>
<button name="update_role">Update</button>
</form>
</td>
<td>
<?php if ($u['role']!='admin'): ?>
<a class="del" href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
<?php else: ?>—<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>
</body>
</html>
