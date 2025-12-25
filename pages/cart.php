<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

/* ---------- ADD TO CART WITH QUANTITY ---------- */
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

    // Check if already in cart
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Update quantity
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$new_quantity, $user_id, $product_id]);
    } else {
        // Insert new item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
}

/* ---------- UPDATE QUANTITY ---------- */
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
}

/* ---------- REMOVE ITEM ---------- */
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

/* ---------- FETCH CART ITEMS ---------- */
$stmt = $conn->prepare("
    SELECT cart.product_id, products.name, products.price, cart.quantity
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_cost = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #eef2f7;
    margin: 0;
}
.cart-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 25px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
h2 { text-align: center; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
th { background: #7a8dbd; color: #fff; }
input[type="number"] { width: 60px; padding: 5px; }
button {
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    background: #7a8dbd;
    color: white;
    cursor: pointer;
}
button:hover { background: #5f74a8; }
.remove-btn { background: #0b0302ff; }
.remove-btn:hover { background: #140301ff; }
.total {
    text-align: right;
    margin-top: 15px;
    font-size: 1.2rem;
    font-weight: bold;
}
.back-link {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #7a8dbd;
    font-weight: bold;
}
</style>
</head>

<body>
<div class="cart-container">
<h2>Your Shopping Cart</h2>

<?php if (empty($cart_items)): ?>
    <p style="text-align:center;">Your cart is empty 🛒</p>
<?php else: ?>
<table>
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
    <th>Action</th>
</tr>

<?php foreach ($cart_items as $item):
    $subtotal = $item['price'] * $item['quantity'];
    $total_cost += $subtotal;
?>
<tr>
    <td><?= htmlspecialchars($item['name']); ?></td>
    <td>$<?= number_format($item['price'], 2); ?></td>
    <td>
        <form method="POST" style="display:flex; gap:6px; justify-content:center;">
            <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
            <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1">
            <button name="update_quantity">Update</button>
        </form>
    </td>
    <td>$<?= number_format($subtotal, 2); ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
            <button name="remove_from_cart" class="remove-btn">Remove</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<div class="total">
    Total: $<?= number_format($total_cost, 2); ?>
</div>
<?php endif; ?>

<a href="../index.php" class="back-link">← Continue Shopping</a>
</div>

</body>
</html>
