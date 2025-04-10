<?php 
// 🚫 Ensure this is the first line
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['login'])==0) {   
    header('location:login.php');
    exit();
}

if (isset($_POST['submit'])) {
    mysqli_query($con,"UPDATE orders SET paymentMethod='".$_POST['paymethod']."' WHERE userId='".$_SESSION['id']."' AND paymentMethod IS NULL");
    unset($_SESSION['cart']);
    header('location:order-history.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shopping Portal | Payment Method</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css">
    <link rel="stylesheet" href="assets/css/lightbox.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/rateit.css">
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet'>
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        .payment-card {
            background-color: white;
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        .payment-card h2 {
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-check { margin-bottom: 15px; }
        .btn-submit { width: 100%; padding: 12px; font-size: 16px; border-radius: 8px; }
        .breadcrumb { background-color: transparent; margin-bottom: 40px; }
        .breadcrumb .breadcrumb-item a { color: #007bff; text-decoration: none; }
        .breadcrumb .breadcrumb-item.active { color: #6c757d; }
        @media (max-width: 576px) { .payment-card { padding: 25px; } }
    </style>
</head>
<body class="cnt-home">

<header class="header-style-1">
    <?php include('includes/top-header.php'); ?>
    <?php include('includes/main-header.php'); ?>
    <?php include('includes/menu-bar.php'); ?>
</header>

<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="home.html">Home</a></li>
                <li class='active'>Payment Method</li>
            </ul>
        </div>
    </div>
</div>

<div class="payment-card">
    <h2>Select Your Payment Method</h2>
    <form method="post">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="paymethod" id="cod" value="COD" checked>
            <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="paymethod" id="internet" value="Internet Banking">
            <label class="form-check-label" for="internet">Internet Banking</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="paymethod" id="card" value="Debit / Credit card">
            <label class="form-check-label" for="card">Debit / Credit Card</label>
        </div>

        <button type="submit" name="submit" class="btn btn-primary mt-4 btn-submit">Cash on Delivery</button><br><br>
        <button type="button" id="rzp-button" class="btn btn-success mt-2 btn-submit">Pay with Razorpay</button>
    </form>
</div>

<?php include('includes/footer.php'); ?>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
    document.getElementById('rzp-button').onclick = function (e) {
        e.preventDefault();
        var options = {
            "key": "rzp_test_8MkpZSt86Wa9F3", // Replace with your test key
            "amount": "200", // Amount in paise = INR 500
            "currency": "INR",
            "name": "My Shop",
            "description": "Order Payment",
            "handler": function (response) {
    // Send payment response to server for verification
    $.ajax({
        url: 'verify-payment.php',
        type: 'POST',
        data: {
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_order_id: response.razorpay_order_id,
            razorpay_signature: response.razorpay_signature
        },
        success: function (data) {
            // You can log or debug 'data' if needed
            window.location.href = 'order-history.php';
        },
        error: function () {
            alert('Payment verification failed. Please contact support.');
        }
    });
},
            "prefill": {
                "name": "<?php echo $_SESSION['login']; ?>",
                "email": "<?php echo $_SESSION['login']; ?>"
            },
            "theme": {
                "color": "#3399cc"
            }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    }
</script>
</body>
</html>
