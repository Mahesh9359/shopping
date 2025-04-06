<?php
session_start();
include_once 'includes/config.php';
$oid = intval($_GET['oid'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking Details</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <style>
    .tracking-container {
        margin: 20px auto;
        max-width: 800px;
        padding: 20px;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .tracking-header {
        color: #e74c3c;
        margin-bottom: 30px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        position: relative; /* Add this */
        min-height: 60px; /* Add this to ensure space for the buttons */
    }
    .tracking-header h2 {
        margin-top: 0; /* Remove default margin */
        padding-top: 5px; /* Add some padding */
    }
    .tracking-header .pull-right {
        position: absolute; /* Change from default to absolute */
        top: 0; /* Position at the top */
        right: 0; /* Align to the right */
    }
    .tracking-table {
        width: 100%;
        margin-bottom: 20px;
    }
    .tracking-table tr td {
        padding: 10px;
        vertical-align: top;
    }
    .tracking-table tr td:first-child {
        font-weight: bold;
        width: 30%;
    }
    .status-delivered {
        color: #27ae60;
        font-weight: bold;
    }
</style>
    <script>
    function printPage() {
        window.print(); 
    }
    function closeWindow() {
        window.close();
    }
    </script>
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
                    <li class='active'>Order Tracking</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="body-content outer-top-xs ">
        <div class="container">
            <div class="tracking-container">
                <div class="tracking-header">
                    <h2>Order Tracking Details</h2>
                    <div class="pull-right">
                        <button onclick="printPage()" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
                        <button onclick="closeWindow()" class="btn btn-default"><i class="fa fa-times"></i> Close</button>
                    </div>
                </div>

                <table class="tracking-table">
                    <tr>
                        <td><b>Order ID:</b></td>
                        <td><?php echo htmlspecialchars($oid); ?></td>
                    </tr>
                    
                    <?php 
                    $ret = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$oid'");
                    $num = mysqli_num_rows($ret);
                    
                    if($num > 0) {
                        while($row = mysqli_fetch_array($ret)) {
                    ?>
                    <tr>
                        <td><b>Date:</b></td>
                        <td><?php echo htmlspecialchars($row['postingDate']); ?></td>
                    </tr>
                    <tr>
                        <td><b>Status:</b></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                    <tr>
                        <td><b>Remark:</b></td>
                        <td><?php echo htmlspecialchars($row['remark']); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr></td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="2">Order not processed yet</td>
                    </tr>
                    <?php 
                    }
                    
                    $st = 'Delivered';
                    $rt = mysqli_query($con, "SELECT orderStatus FROM orders WHERE id='$oid'");
                    $currentSt = '';
                    if($row = mysqli_fetch_array($rt)) {
                        $currentSt = $row['orderStatus'];
                    }
                    
                    if($st == $currentSt) { 
                    ?>
                    <tr>
                        <td colspan="2" class="status-delivered">
                            <i class="fa fa-check-circle"></i> Product delivered successfully
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>