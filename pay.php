<?php
session_start();
include('include/config.php');
include('include/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay with Razorpay</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="./css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<?php include('include/sidebar.php'); ?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="span9">
                <div class="content">
                    <div class="module">
                        <div class="module-head">
                            <h3>Make a Payment</h3>
                        </div>
                        <div class="module-body">
                            <p>You can pay ₹100 securely using Razorpay in test mode.</p>
                            <button id="pay-btn" class="btn btn-success">Pay ₹100</button>
                        </div>
                    </div>
                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div>
</div>

<?php include('include/footer.php'); ?>

<script src="scripts/jquery-1.9.1.min.js"></script>
<script>
    document.getElementById('pay-btn').onclick = function () {
        var options = {
            "key": "rzp_test_YourTestKeyHere", // Replace with your test key from Razorpay dashboard
            "amount": "10000", // 100 x 100 paise = ₹100
            "currency": "INR",
            "name": "FreeBasket",
            "description": "Test Transaction",
            "image": "https://yourdomain.com/logo.png", // Optional
            "handler": function (response) {
                alert("Payment successful! Payment ID: " + response.razorpay_payment_id);
                // You can redirect to thank you page or store payment info here
            },
            "prefill": {
                "name": "Test User",
                "email": "test@example.com",
                "contact": "9999999999"
            },
            "theme": {
                "color": "#3399cc"
            }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    };
</script>

</body>
</html>
