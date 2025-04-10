<?php 
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize variables with proper checks
$user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
$pdtid = array();
$is_logged_in = isset($_SESSION['login']) && !empty($_SESSION['login']);

// Update cart quantities
if(isset($_POST['submit'])){
    if(!empty($_SESSION['cart'])){
        foreach($_POST['quantity'] as $key => $val){
            $key = intval($key);
            $val = intval($val);
            if($val <= 0){
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }
        echo "<script>alert('Your Cart has been Updated');</script>";
    }
}

// Remove products from cart
if(isset($_POST['remove_code'])){
    if(!empty($_SESSION['cart'])){
        foreach($_POST['remove_code'] as $key){
            $key = intval($key);
            unset($_SESSION['cart'][$key]);
        }
        echo "<script>alert('Your Cart has been Updated');</script>";
    }
}

// Process order submission
if(isset($_POST['ordersubmit'])) {
    if(!$is_logged_in){
        header('location:login.php');
        exit;
    }
    
    if(!empty($_POST['quantity']) && !empty($_SESSION['pid'])){
        $quantity = array_map('intval', $_POST['quantity']);
        $pdd = array_map('intval', $_SESSION['pid']);
        
        if(count($pdd) == count($quantity)){
            foreach(array_combine($pdd, $quantity) as $qty => $val34){
                mysqli_query($con,"INSERT INTO orders(userId,productId,quantity) VALUES('$user_id','$qty','$val34')");
            }
            
            // Store a flag in session to track order submission
            $_SESSION['order_submitted'] = true;
            
            // Use JavaScript to open payment-method.php in a new tab
            echo "<script>
                window.open('payment-method.php', '_blank');
                window.location.href = 'my-cart.php'; // redirect current page to cart or order history
            </script>";
            exit;
        }
    }
}

// Update billing address
if(isset($_POST['update']) && $is_logged_in){
    $baddress = mysqli_real_escape_string($con, $_POST['billingaddress'] ?? '');
    $bstate = mysqli_real_escape_string($con, $_POST['bilingstate'] ?? '');
    $bcity = mysqli_real_escape_string($con, $_POST['billingcity'] ?? '');
    $bpincode = mysqli_real_escape_string($con, $_POST['billingpincode'] ?? '');
    
    $query = mysqli_query($con,"UPDATE users SET billingAddress='$baddress',billingState='$bstate',billingCity='$bcity',billingPincode='$bpincode' WHERE id='$user_id'");
    if($query){
        echo "<script>alert('Billing Address has been updated');</script>";
    }
}

// Update shipping address
if(isset($_POST['shipupdate']) && $is_logged_in){
    $saddress = mysqli_real_escape_string($con, $_POST['shippingaddress'] ?? '');
    $sstate = mysqli_real_escape_string($con, $_POST['shippingstate'] ?? '');
    $scity = mysqli_real_escape_string($con, $_POST['shippingcity'] ?? '');
    $spincode = mysqli_real_escape_string($con, $_POST['shippingpincode'] ?? '');
    
    $query = mysqli_query($con,"UPDATE users SET shippingAddress='$saddress',shippingState='$sstate',shippingCity='$scity',shippingPincode='$spincode' WHERE id='$user_id'");
    if($query){
        echo "<script>alert('Shipping Address has been updated');</script>";
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

<?php include('includes/brands-slider.php');?>
<?php include('includes/footer.php');?>

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
    const cartForm = document.getElementById("cart");

    const formData = new FormData(cartForm);
    formData.append("ordersubmit", "true");

    fetch("my-cart.php", {
        method: "POST",
        body: formData
    }).then(res => {
        // Open new tab for payment
        window.open("payment-method.php", "_blank");
        // Reload cart page to clear items if needed
        window.location.href = "my-cart.php";
    });
});
</script>

</body>
</html>