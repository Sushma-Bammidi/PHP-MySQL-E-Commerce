<?php
session_start();

// Remove only admin-related session variables
unset($_SESSION['admin_id']);
unset($_SESSION['is_admin']);

// Optional: regenerate session ID for extra safety
session_regenerate_id(true);

// Redirect to admin login
header("Location: login.php");
exit();
?>
