<?php
session_start();
include_once 'include/config.php';

// Redirect if not logged in
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
    exit();
}

$oid = intval($_GET['oid']);

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit2'])) {
    // Validate and sanitize inputs
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $remark = mysqli_real_escape_string($con, $_POST['remark']);
    
    // Verify order exists
    $orderCheck = mysqli_query($con, "SELECT id, is_grouped FROM orders WHERE id='$oid'");
    if(mysqli_num_rows($orderCheck) == 0) {
        die("Invalid order ID");
    }
    
    // Begin transaction for atomic updates
    mysqli_begin_transaction($con);
    
    try {
        // Check if this is a parent order
        $isParent = mysqli_query($con, "SELECT id FROM orders WHERE id='$oid' AND is_grouped=1");
        
        if(mysqli_num_rows($isParent) > 0) {
            // Update all child orders
            if(!mysqli_query($con, "UPDATE orders SET orderStatus='$status' WHERE parent_order_id='$oid'")) {
                throw new Exception("Failed to update child orders");
            }
        }
        
        // Update main order
        if(!mysqli_query($con, "UPDATE orders SET orderStatus='$status' WHERE id='$oid'")) {
            throw new Exception("Failed to update order status");
        }
        
        // Add tracking history
        $trackHistory = "INSERT INTO ordertrackhistory(orderId, status, remark, postingDate) 
                         VALUES('$oid', '$status', '$remark', NOW())";
        if(!mysqli_query($con, $trackHistory)) {
            throw new Exception("Failed to add tracking history");
        }
        
        // Commit transaction if all queries succeeded
        mysqli_commit($con);
        
        // Success - redirect or show message
        $_SESSION['success_msg'] = "Order #$oid updated successfully";
        header("Location: todays-orders.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($con);
        $error = "Error updating order: " . $e->getMessage();
    }
}

// Fetch order details for display
$orderDetails = mysqli_query($con, "SELECT * FROM orders WHERE id='$oid'");
if(mysqli_num_rows($orderDetails) == 0) {
    die("Order not found");
}
$order = mysqli_fetch_assoc($orderDetails);

// Fetch tracking history
$trackHistory = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$oid' ORDER BY postingDate DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| Update Order #<?php echo $oid; ?></title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .fontkink1 { font-weight: bold; padding-right: 10px; }
        .fontkink { color: #333; }
        .form-control {
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        }
        .status-form {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin-top: 20px;
        }
        textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
        .alert { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .alert-error { background-color: #f2dede; border-color: #ebccd1; color: #a94442; }
        .alert-success { background-color: #dff0d8; border-color: #d6e9c6; color: #3c763d; }
    </style>
</head>
<body>
    <?php include('include/header.php'); ?>

    <div class="wrapper">
        <div class="container">
            <div class="row">
                <?php include('include/sidebar.php'); ?>                
                <div class="span9">
                    <div class="content">
                        <div class="module">
                            <div class="module-head">
                                <h3>Update Order #<?php echo $oid; ?></h3>
                            </div>
                            <div class="module-body">
                                <?php if(isset($error)): ?>
                                    <div class="alert alert-error"><?php echo $error; ?></div>
                                <?php endif; ?>
                                
                                <div class="status-form">
                                    <form name="updateticket" id="updateticket" method="post"> 
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr height="50">
                                                <td colspan="2" style="padding-left:0px;">
                                                    <div style="font-size:16px;font-weight:bold;color:#0066cc;">
                                                        <b>Order Details</b>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr height="30">
                                                <td class="fontkink1"><b>Order ID:</b></td>
                                                <td class="fontkink"><?php echo $oid; ?></td>
                                            </tr>
                                            <tr height="30">
                                                <td class="fontkink1"><b>Current Status:</b></td>
                                                <td class="fontkink"><?php echo $order['orderStatus']; ?></td>
                                            </tr>
                                            
                                            <?php if(mysqli_num_rows($trackHistory) > 0): ?>
                                                <tr>
                                                    <td colspan="2"><hr /></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <h4>Status History</h4>
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>Status</th>
                                                                <th>Remark</th>
                                                                <th>Date</th>
                                                            </tr>
                                                            <?php while($history = mysqli_fetch_assoc($trackHistory)): ?>
                                                            <tr>
                                                                <td><?php echo $history['status']; ?></td>
                                                                <td><?php echo $history['remark']; ?></td>
                                                                <td><?php echo $history['postingDate']; ?></td>
                                                            </tr>
                                                            <?php endwhile; ?>
                                                        </table>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            
                                            <?php if($order['orderStatus'] != 'Delivered'): ?>
                                                <tr>
                                                    <td colspan="2"><hr /></td>
                                                </tr>
                                                <tr height="50">
                                                    <td class="fontkink1">Status: </td>
                                                    <td class="fontkink">
                                                        <div class="control-group">
                                                            <div class="controls">
                                                                <select name="status" class="form-control span4" required>
                                                                    <option value="">Select Status</option>
                                                                    <option value="Pending" <?php echo ($order['orderStatus']=='Pending')?'selected':''; ?>>Pending</option>
                                                                    <option value="Processing" <?php echo ($order['orderStatus']=='Processing')?'selected':''; ?>>Processing</option>
                                                                    <option value="Shipped" <?php echo ($order['orderStatus']=='Shipped')?'selected':''; ?>>Shipped</option>
                                                                    <option value="Out for Delivery" <?php echo ($order['orderStatus']=='Out for Delivery')?'selected':''; ?>>Out for Delivery</option>
                                                                    <option value="Delivered" <?php echo ($order['orderStatus']=='Delivered')?'selected':''; ?>>Delivered</option>
                                                                    <option value="Cancelled" <?php echo ($order['orderStatus']=='Cancelled')?'selected':''; ?>>Cancelled</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fontkink1">Remark:</td>
                                                    <td class="fontkink" align="justify">
                                                        <textarea cols="50" rows="4" name="remark" required placeholder="Enter update remarks..."></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fontkink1">&nbsp;</td>
                                                    <td class="fontkink">
                                                        <button type="submit" name="submit2" class="btn btn-primary">
                                                            <i class="icon-ok icon-white"></i> Update Status
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="window.close();">
                                                            <i class="icon-remove icon-white"></i> Close
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="2" class="alert alert-success">
                                                        <i class="icon-ok icon-white"></i> This order has been delivered.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('include/footer.php'); ?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>