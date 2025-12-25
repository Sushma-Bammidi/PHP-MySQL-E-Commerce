<?php
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

/* ---------------- DELETE PRODUCT ---------------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php");
    exit();
}

/* ---------------- ADD / UPDATE PRODUCT ---------------- */
if (isset($_POST['save_product'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $id = $_POST['id'] ?? '';

    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../images/" . $image);
    } else {
        $image = $_POST['old_image'] ?? '';
    }

    if ($id) {
        // Update
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?");
        $stmt->execute([$name, $price, $description, $image, $id]);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $description, $image]);
    }

    header("Location: products.php");
    exit();
}

/* ---------------- FETCH PRODUCT FOR EDIT ---------------- */
$editProduct = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ---------------- FETCH ALL PRODUCTS ---------------- */
$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Products</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{font-family:'Segoe UI';margin:0;background:#e9eef8;}
.container{max-width:1100px;margin:30px auto;background:#fff;padding:25px;border-radius:14px;}
h2{color:#2f3e55}
form{margin-bottom:25px;background:#f6f9ff;padding:20px;border-radius:12px;}
input,textarea{width:100%;padding:10px;margin:8px 0;border:1px solid #ccc;border-radius:6px;}
button{background:#7a8dbd;color:#fff;border:none;padding:10px 18px;border-radius:6px;cursor:pointer;}
button:hover{background:#5f74a8;}
table{width:100%;border-collapse:collapse;}
th,td{padding:10px;border-bottom:1px solid #ddd;}
img{width:60px;border-radius:6px;}
a{color:#fff;padding:6px 10px;border-radius:5px;text-decoration:none;font-size:14px;}
.edit{background:#3498db;}
.del{background:#e74c3c;}
.edit:hover{background:#2980b9;}
.del:hover{background:#c0392b;}
.top{display:flex;justify-content:space-between;align-items:center;}
.back{background:#7a8dbd;}
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
<h2>📦 Manage Products</h2>
<a href="dashboard.php" class="back">← Dashboard</a>
</div>

<!-- ADD / EDIT FORM -->
<form method="POST" enctype="multipart/form-data">
<h3><?= $editProduct ? "✏️ Edit Product" : "➕ Add Product"; ?></h3>

<input type="hidden" name="id" value="<?= $editProduct['id'] ?? '' ?>">
<input type="hidden" name="old_image" value="<?= $editProduct['image'] ?? '' ?>">

<input type="text" name="name" placeholder="Product Name" required
value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">

<input type="number" step="0.01" name="price" placeholder="Price" required
value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>">

<textarea name="description" placeholder="Description" required><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>

<input type="file" name="image">

<button type="submit" name="save_product">
<?= $editProduct ? "Update Product" : "Add Product"; ?>
</button>
</form>

<!-- PRODUCTS TABLE -->
<table>
<tr>
<th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Actions</th>
</tr>

<?php foreach ($products as $p): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><?php if($p['image']): ?><img src="../images/<?= $p['image'] ?>"><?php endif; ?></td>
<td><?= htmlspecialchars($p['name']) ?></td>
<td>$<?= number_format($p['price'],2) ?></td>
<td>
<a class="edit" href="?edit=<?= $p['id'] ?>">Edit</a>
<a class="del" href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>

</td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
