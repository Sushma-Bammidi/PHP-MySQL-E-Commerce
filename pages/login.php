<?php
include('../includes/db.php');  // Include the database connection
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        header("Location: ../index.php"); // Redirect to the main page
        exit();
    } else {
        // Invalid login
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<style>
* {
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, LightSteelBlue, #e6ecf5);
}

/* Card */
.login-container {
    background: #ffffff;
    padding: 35px 30px;
    width: 100%;
    max-width: 420px;
    border-radius: 14px;
    box-shadow: 0 18px 35px rgba(100, 120, 160, 0.25);
    animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(25px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2f3e55;
    font-size: 1.9rem;
}

/* Form */
label {
    display: block;
    margin-bottom: 6px;
    color: #4a5d78;
    font-weight: 500;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid #c2cce0;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input::placeholder {
    color: #9aa7bf;
}

input:focus {
    border-color: #7a8dbd;
    box-shadow: 0 0 0 3px rgba(122, 141, 189, 0.3);
    outline: none;
}

/* Button */
button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #7a8dbd, #5f74a8);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(95, 116, 168, 0.35);
}

button:active {
    transform: scale(0.97);
}

/* Error */
.error-message {
    margin-top: 15px;
    text-align: center;
    color: #c0392b;
    font-weight: 500;
}

/* Footer */
.footer-text {
    margin-top: 20px;
    text-align: center;
    font-size: 0.9rem;
    color: #5c6f8c;
}

.footer-text a {
    color: #5f74a8;
    text-decoration: none;
    font-weight: 500;
}

.footer-text a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="login-container">
    <h2>Welcome Back</h2>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <button type="submit" name="login">Login</button>
    </form>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <div class="footer-text">
        Don’t have an account? <a href="register.php">Register</a>
    </div>
</div>

</body>
</html>
