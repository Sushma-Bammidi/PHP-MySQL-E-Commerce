<?php
session_start();
include '../includes/db.php';

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            // Secure the session
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['is_admin'] = true;

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials or not an admin.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    background: linear-gradient(135deg, #c9d6ff, #e2e2e2);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

.login-container {
    width: 100%;
    max-width: 420px;
    background: #fff;
    padding: 35px 30px;
    border-radius: 14px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.login-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2f3e55;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #555;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

input:focus {
    outline: none;
    border-color: #7a8dbd;
    box-shadow: 0 0 0 2px rgba(122,141,189,0.25);
}

button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: #7a8dbd;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}


button:hover {
    background: #5f74a8;
}

.error {
    background: #fdecea;
    color: #150503ff;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 15px;
    font-size: 14px;
}
</style>
</head>

<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
