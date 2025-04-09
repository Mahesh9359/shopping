<?php
session_start();

// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if cart array is initialized
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Include config and header/menu files
include('/includes/config.php');
include('/includes/top-header.php');
include('/includes/main-header.php');
include('/includes/menu-bar.php');
include('/includes/side-menu.php');

// Debug checkpoint
echo "<!-- Checkpoint: includes loaded -->";

// Handle Add to Cart
if (isset($_GET['action']) && $_GET['action'] === "add") {
    $id = intval($_GET['id'] ?? 0);

    if ($id > 0) {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = array("quantity" => 1);
        } else {
            $_SESSION['cart'][$id]['quantity']++;
        }
        echo "<script>alert('Product has been added to the cart');</script>";
        echo "<script>window.location='index.php'</script>";
        exit;
    }
}
?>

<!-- Display Products -->
<div class="container">
    <h2>Featured Products</h2>

    <div class="row">
        <?php
        $query = "SELECT * FROM products ORDER BY id DESC LIMIT 6";
        $result = mysqli_query($con, $query);

        if (!$result) {
            echo "<p>Error fetching products: " . mysqli_error($con) . "</p>";
        } else {
            if (mysqli_num_rows($result) === 0) {
                echo "<p>No products found.</p>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-4">
                        <div class="card" style="margin-bottom: 20px;">
                            <img src="/admin/productimages/<?php echo htmlentities($row['id']); ?>/<?php echo htmlentities($row['productImage1']); ?>"
                                 alt="<?php echo htmlentities($row['productName']); ?>"
                                 style="width: 100%; height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlentities($row['productName']); ?></h5>
                                <p class="card-text">Price: ₹<?php echo htmlentities($row['productPrice']); ?></p>
                                <a href="/index.php?page=product&action=add&id=<?php echo htmlentities($row['id']); ?>"
                                   class="btn btn-primary">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>
</div>

<?php include('/includes/footer.php'); ?>
