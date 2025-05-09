<?php
session_start();
error_reporting(E_ALL);
$pdtid = array();
ini_set('display_errors', 1); // Remove in production
include('includes/config.php');

// Initialize variables safely
$user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
$pdtid = array(); // Explicitly initialize as array
$is_logged_in = isset($_SESSION['login']) && !empty($_SESSION['login']);
// ================== UPDATE CART QUANTITIES ==================
if (isset($_POST['submit'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_POST['quantity'] as $key => $val) {
            $key = intval($key);
            $val = intval($val);
            
            if ($val <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }
        $_SESSION['toast_success'] = 'Cart updated successfully!';
    }
}

// ================== REMOVE ITEMS FROM CART ==================
if (isset($_POST['remove_code'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_POST['remove_code'] as $key) {
            $key = intval($key);
            unset($_SESSION['cart'][$key]);
        }
        $_SESSION['toast_success'] = 'Item(s) removed from cart!';
    }
}

// ================== PROCESS ORDER SUBMISSION ==================
// ================== PROCESS ORDER SUBMISSION ==================
if (isset($_POST['ordersubmit'])) {
    if (!$is_logged_in) {
        $_SESSION['toast_error'] = 'Please login to checkout!';
        header('Location: login.php');
        exit;
    }

    if (empty($_SESSION['cart'])) {
        $_SESSION['toast_error'] = 'Your cart is empty!';
        header('Location: my-cart.php');
        exit;
    }

    // Check if addresses are complete
    $addressCheck = mysqli_query($con, "SELECT billingAddress, billingState, billingCity, billingPincode, billingPhone,
    shippingAddress, shippingState, shippingCity, shippingPincode, shippingPhone 
    FROM users WHERE id='$user_id'");
$addresses = mysqli_fetch_assoc($addressCheck);

$billingComplete = !empty($addresses['billingAddress']) && !empty($addresses['billingState']) && 
!empty($addresses['billingCity']) && !empty($addresses['billingPincode']) &&
!empty($addresses['billingPhone']);

$shippingComplete = !empty($addresses['shippingAddress']) && !empty($addresses['shippingState']) && 
!empty($addresses['shippingCity']) && !empty($addresses['shippingPincode']) &&
!empty($addresses['shippingPhone']);
    
    if (!$billingComplete || !$shippingComplete) {
        $errorMsg = [];
        if (!$billingComplete) $errorMsg[] = "Billing address is incomplete";
        if (!$shippingComplete) $errorMsg[] = "Shipping address is incomplete";
        
        $_SESSION['toast_error'] = implode(" and ", $errorMsg) . ". Please update your addresses before checkout.";
        header('Location: my-cart.php');
        exit;
    }

    // Store cart temporarily for payment page
    $_SESSION['pending_order'] = [
        'products' => $_SESSION['cart'],
        'total' => array_reduce($_SESSION['cart'], function($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']) + $item['shipping'];
        }, 0)
    ];

    // Redirect to payment (NO database insert yet!)
    header('Location: payment-method.php');
    exit;
}

// Update billing address
if(isset($_POST['update']) && $is_logged_in){
    $baddress = mysqli_real_escape_string($con, $_POST['billingaddress'] ?? '');
    $bstate = mysqli_real_escape_string($con, $_POST['bilingstate'] ?? '');
    $bcity = mysqli_real_escape_string($con, $_POST['billingcity'] ?? '');
    $bpincode = mysqli_real_escape_string($con, $_POST['billingpincode'] ?? '');
    $bphone = mysqli_real_escape_string($con, $_POST['billingphone'] ?? '');
    
    $query = mysqli_query($con,"UPDATE users SET 
        billingAddress='$baddress',
        billingState='$bstate',
        billingCity='$bcity',
        billingPincode='$bpincode',
        billingPhone='$bphone'
        WHERE id='$user_id'");
    
    if($query){
        $_SESSION['toast_success'] = 'Billing address updated successfully!';
    } else {
        $_SESSION['toast_error'] = 'Error updating billing address: ' . mysqli_error($con);
    }
}

// Update shipping address
if(isset($_POST['shipupdate']) && $is_logged_in){
    $saddress = mysqli_real_escape_string($con, $_POST['shippingaddress'] ?? '');
    $sstate = mysqli_real_escape_string($con, $_POST['shippingstate'] ?? '');
    $scity = mysqli_real_escape_string($con, $_POST['shippingcity'] ?? '');
    $spincode = mysqli_real_escape_string($con, $_POST['shippingpincode'] ?? '');
    $sphone = mysqli_real_escape_string($con, $_POST['shippingphone'] ?? '');
    
    $query = mysqli_query($con,"UPDATE users SET 
        shippingAddress='$saddress',
        shippingState='$sstate',
        shippingCity='$scity',
        shippingPincode='$spincode',
        shippingPhone='$sphone'
        WHERE id='$user_id'");
    
    if($query){
        $_SESSION['toast_success'] = 'Shipping address updated successfully!';
    } else {
        $_SESSION['toast_error'] = 'Error updating shipping address: ' . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>My Cart</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css">
    <link href="assets/css/lightbox.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/rateit.css">
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="assets/images/favicon.ico">
</head>
<body class="cnt-home">
<header class="header-style-1">
    <?php include('includes/top-header.php');?>
    <?php include('includes/main-header.php');?>
    <?php include('includes/menu-bar.php');?>
</header>

<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="index.php">Home</a></li>
                <li class='active'>Shopping Cart</li>
            </ul>
        </div>
    </div>
</div>

<div class="body-content outer-top-xs">
    <div class="container">
        <div class="row inner-bottom-sm">
            <div class="shopping-cart">
                <div class="col-md-12 col-sm-12 shopping-cart-table">
                    <div class="table-responsive">
                        <form name="cart" method="post" id="cart">
                            <?php if(!empty($_SESSION['cart'])): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="cart-romove item">Remove</th>
                                        <th class="cart-description item">Image</th>
                                        <th class="cart-product-name item">Product Name</th>
                                        <th class="cart-qty item">Quantity</th>
                                        <th class="cart-sub-total item">Price Per unit</th>
                                        <th class="cart-sub-total item">Shipping Charge</th>
                                        <th class="cart-total last-item">Grandtotal</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">
                                            <div class="shopping-cart-btn">
                                                <span class="">
                                                    <a href="index.php" class="btn btn-upper btn-primary outer-left-xs">Continue Shopping</a>
                                                    <input type="submit" name="submit" value="Update shopping cart" class="btn btn-upper btn-primary pull-right outer-right-xs">
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $product_ids = array_map('intval', array_keys($_SESSION['cart']));
                                    $sql = "SELECT * FROM products WHERE id IN(".implode(',', $product_ids).") ORDER BY id ASC";
                                    $query = mysqli_query($con,$sql);
                                    $totalprice = 0;
                                    $totalqunty = 0;
                                    
                                    if($query && mysqli_num_rows($query) > 0){
                                        while($row = mysqli_fetch_array($query)){
                                            $productId = $row['id'];
                                            $quantity = $_SESSION['cart'][$productId]['quantity'] ?? 0;
                                            $subtotal = $quantity * $row['productPrice'] + $row['shippingCharge'];
                                            $totalprice += $subtotal;
                                            $_SESSION['qnty'] = $totalqunty += $quantity;
                                            
                                            // Now this will work since $pdtid is initialized
                                            array_push($pdtid, $productId);
                                    ?>
                                    <tr>
                                        <td class="romove-item"><input type="checkbox" name="remove_code[]" value="<?php echo $productId; ?>" /></td>
                                        <td class="cart-image">
                                            <a class="entry-thumbnail" href="product-details.php?pid=<?php echo $productId; ?>">
                                                <img src="admin/productimages/<?php echo $productId; ?>/<?php echo htmlentities($row['productImage1']); ?>" alt="" width="114" height="146">
                                            </a>
                                        </td>
                                        <td class="cart-product-name-info">
                                            <h4 class='cart-product-description'>
                                                <a href="product-details.php?pid=<?php echo $productId; ?>">
                                                    <?php echo htmlentities($row['productName']); ?>
                                                </a>
                                            </h4>
                                            <?php 
                                            $rt = mysqli_query($con,"SELECT * FROM productreviews WHERE productId='$productId'");
                                            if($rt && mysqli_num_rows($rt) > 0):
                                            ?>
                                            <div class="reviews">(<?php echo mysqli_num_rows($rt); ?> Reviews)</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="cart-product-quantity">
                                            <div class="quant-input">
                                                <div class="arrows">
                                                    <div class="arrow plus gradient"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
                                                    <div class="arrow minus gradient"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
                                                </div>
                                                <input type="number" min="0" value="<?php echo $quantity; ?>" name="quantity[<?php echo $productId; ?>]">
                                            </div>
                                        </td>
                                        <td class="cart-product-sub-total"><span class="cart-sub-total-price">Rs <?php echo number_format($row['productPrice'], 2); ?></span></td>
                                        <td class="cart-product-sub-total"><span class="cart-sub-total-price">Rs <?php echo number_format($row['shippingCharge'], 2); ?></span></td>
                                        <td class="cart-product-grand-total"><span class="cart-grand-total-price">Rs <?php echo number_format($subtotal, 2); ?></span></td>
</tr>
<?php 
    // Stock availability check
    $stockCheck = mysqli_query($con, "SELECT stock FROM products WHERE id = $productId");
    if($stock = mysqli_fetch_assoc($stockCheck)) {
        if($stock['stock'] < $quantity) {
            echo "<tr><td colspan='7'><div class='alert alert-warning'>Only {$stock['stock']} available in stock for product '".htmlentities($row['productName'])."'</div></td></tr>";
        }
    }
}
$_SESSION['pid'] = $pdtid;
} 
?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <div class="alert alert-info">Your shopping Cart is empty</div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Billing Address Section (Kept Intact) -->
                <div class="col-md-4 col-sm-12 estimate-ship-tax">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><span class="estimate-title">Billing Address</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <form method="post">
                                        <?php
                                        if($is_logged_in){
                                            $query = mysqli_query($con,"SELECT * FROM users WHERE id='$user_id'");
                                            if($query && $row = mysqli_fetch_array($query)){
                                        ?>
<!-- In both billing and shipping address forms -->
<div class="form-group">
    <label class="info-title">Billing Address<span>*</span></label>
    <textarea class="form-control unicase-form-control text-input" name="billingaddress" required><?php echo htmlentities($row['billingAddress'] ?? ''); ?></textarea>
</div>
<div class="form-group">
    <label class="info-title">Billing State<span>*</span></label>
    <input type="text" class="form-control unicase-form-control text-input" name="bilingstate" value="<?php echo htmlentities($row['billingState'] ?? ''); ?>" required>
</div>
<div class="form-group">
    <label class="info-title">Billing City<span>*</span></label>
    <input type="text" class="form-control unicase-form-control text-input" name="billingcity" value="<?php echo htmlentities($row['billingCity'] ?? ''); ?>" required>
</div>
<div class="form-group">
    <label class="info-title">Billing Pincode<span>*</span></label>
    <input type="text" class="form-control unicase-form-control text-input" name="billingpincode" value="<?php echo htmlentities($row['billingPincode'] ?? ''); ?>" required>
</div>
<div class="form-group">
    <label class="info-title">Billing Phone<span>*</span></label>
    <input type="tel" class="form-control unicase-form-control text-input" name="billingphone" 
           value="<?php echo htmlentities($row['billingPhone'] ?? ''); ?>" required>
</div>
                                        <button type="submit" name="update" class="btn-upper btn btn-primary checkout-page-button">Update</button>
                                        <?php 
                                            }
                                        } else {
                                            echo '<p>Please <a href="login.php">login</a> to manage your billing address</p>';
                                        }
                                        ?>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Shipping Address Section (Kept Intact) -->
                <div class="col-md-4 col-sm-12 estimate-ship-tax">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><span class="estimate-title">Shipping Address</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <form method="post">
                                        <?php
                                        if($is_logged_in){
                                            $query = mysqli_query($con,"SELECT * FROM users WHERE id='$user_id'");
                                            if($query && $row = mysqli_fetch_array($query)){
                                        ?>
                                        <div class="form-group">
                                            <label class="info-title">Shipping Address<span>*</span></label>
                                            <textarea class="form-control unicase-form-control text-input" name="shippingaddress" required><?php echo htmlentities($row['shippingAddress'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="info-title">Shipping State<span>*</span></label>
                                            <input type="text" class="form-control unicase-form-control text-input" name="shippingstate" value="<?php echo htmlentities($row['shippingState'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="info-title">Shipping City<span>*</span></label>
                                            <input type="text" class="form-control unicase-form-control text-input" name="shippingcity" value="<?php echo htmlentities($row['shippingCity'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="info-title">Shipping Pincode<span>*</span></label>
                                            <input type="text" class="form-control unicase-form-control text-input" name="shippingpincode" value="<?php echo htmlentities($row['shippingPincode'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group">
    <label class="info-title">Shipping Phone<span>*</span></label>
    <input type="tel" class="form-control unicase-form-control text-input" name="shippingphone" 
           value="<?php echo htmlentities($row['shippingPhone'] ?? ''); ?>" required>
</div>
                                        <button type="submit" name="shipupdate" class="btn-upper btn btn-primary checkout-page-button">Update</button>
                                        <?php 
                                            }
                                        } else {
                                            echo '<p>Please <a href="login.php">login</a> to manage your shipping address</p>';
                                        }
                                        ?>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php if(!empty($_SESSION['cart'])): ?>
                <div class="col-md-4 col-sm-12 cart-shopping-total">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <div class="cart-grand-total">
                                        Grand Total<span class="inner-left-md">Rs <?php echo number_format($totalprice, 2); ?></span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="cart-checkout-btn pull-right">
                                        <?php if($is_logged_in): ?>
                                            <button type="button" class="btn btn-primary" id="checkout-btn">PROCEED TO CHECKOUT</button>

                                        <?php else: ?>
                                        <a href="login.php" class="btn btn-primary">LOGIN TO CHECKOUT</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php include('includes/footer.php');?>
<?php if (isset($_SESSION['toast_success'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '<?php echo addslashes($_SESSION['toast_success']); ?>',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
});
</script>
<?php unset($_SESSION['toast_success']); endif; ?>
<?php if (isset($_SESSION['toast_error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: '<?php echo addslashes($_SESSION['toast_error']); ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
                });
                });
                </script>
                <?php unset($_SESSION['toast_error']); endif; ?>
                
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/echo.min.js"></script>
<script src="assets/js/jquery.easing-1.3.min.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script>
<script src="assets/js/jquery.rateit.min.js"></script>
<script src="assets/js/lightbox.min.js"></script>
<script src="assets/js/bootstrap-select.min.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/scripts.js"></script>
<script>
document.getElementById("checkout-btn").addEventListener("click", function () {
    <?php if($is_logged_in): ?>
        // Check if addresses are filled
        <?php 
        $query = mysqli_query($con,"SELECT billingAddress, billingState, billingCity, billingPincode, 
                                  shippingAddress, shippingState, shippingCity, shippingPincode 
                                  FROM users WHERE id='$user_id'");
        $addresses = mysqli_fetch_assoc($query);
        $billingComplete = !empty($addresses['billingAddress']) && !empty($addresses['billingState']) && 
                          !empty($addresses['billingCity']) && !empty($addresses['billingPincode']);
        $shippingComplete = !empty($addresses['shippingAddress']) && !empty($addresses['shippingState']) && 
                          !empty($addresses['shippingCity']) && !empty($addresses['shippingPincode']);
        ?>
        
        <?php if(!$billingComplete || !$shippingComplete): ?>
            Swal.fire({
                icon: 'error',
                title: 'Address Required',
                html: `<?php 
                    $messages = [];
                    if(!$billingComplete) $messages[] = "Please complete your billing address";
                    if(!$shippingComplete) $messages[] = "Please complete your shipping address";
                    echo implode("<br>", $messages);
                ?>`,
                confirmButtonText: 'OK'
            });
            return false;
        <?php endif; ?>
    <?php endif; ?>

    Swal.fire({
        title: 'Are you sure?',
        text: "You will be redirected to the payment page.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const cartForm = document.getElementById("cart");
            const formData = new FormData(cartForm);
            formData.append("ordersubmit", "true");

            fetch("my-cart.php", {
                method: "POST",
                body: formData
            }).then(res => {
                // Show a toast before redirecting
                Swal.fire({
                    icon: 'success',
                    title: 'Redirecting to payment...',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3200
                });

                setTimeout(() => {
                    window.open("payment-method.php", "_blank");
                    window.location.href = "my-cart.php";
                }, 1200);
            });
        }
    });
});
// Phone number validation
document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9+]/g, '');
    });
});
</script>


</body>
</html>