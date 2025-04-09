<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin']) == 0) {    
    header('location:index.php');
    exit;
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Pending Orders</title>
    <link type="text/css" href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="/css/theme.css" rel="stylesheet">
    <link type="text/css" href="/images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }
        .datatable-1 {
            min-width: 1200px;
            display: table !important; /* Forces visibility */
        }
        .no-orders {
            padding: 15px;
            text-align: center;
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
<?php include('include/header.php');?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <?php include('include/sidebar.php');?>                
            <div class="span9">
                <div class="content">
                    <div class="module">
                        <div class="module-head">
                            <h3>Pending Orders</h3>
                        </div>
                        <div class="module-body">
                            <?php if(isset($_GET['del'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                    <?php unset($_SESSION['delmsg']); ?>
                                </div>
                            <?php } ?>

                            <br />

                            <?php
                            $query = mysqli_query($con, "SELECT 
    users.name AS username,
    users.email AS useremail,
    users.contactno AS usercontact,
    CONCAT(
        COALESCE(users.shippingAddress, 'N/A'), ', ', 
        COALESCE(users.shippingCity, 'N/A'), ', ', 
        COALESCE(users.shippingState, 'N/A'), '-', 
        COALESCE(users.shippingPincode, '000000')
    ) AS fulladdress,
    products.productName AS productname,
    products.shippingCharge AS shippingcharge,
    orders.quantity AS quantity,
    orders.orderDate AS orderdate,
    products.productPrice AS productprice,
    orders.id AS id,
    orders.orderStatus AS status
FROM orders 
JOIN users ON orders.userId = users.id 
JOIN products ON products.id = orders.productId 
WHERE orders.orderStatus != 'Delivered' OR orders.orderStatus IS NULL
ORDER BY orders.orderDate DESC
");
                            
                            if(mysqli_num_rows($query) > 0) { ?>
                                <div class="table-responsive">
                                    <table id="ordersTable" cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email / Contact</th>
                                                <th>Shipping Address</th>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Amount</th>
                                                <th>Order Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $cnt = 1;
                                            while($row = mysqli_fetch_array($query)) {
                                                $amount = $row['quantity'] * $row['productprice'] + $row['shippingcharge'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo htmlentities($row['username']); ?></td>
                                                    <td><?php echo htmlentities($row['useremail']); ?><br><?php echo htmlentities($row['usercontact']); ?></td>
                                                    <td><?php echo !empty($row['fulladdress']) ? htmlentities($row['fulladdress']) : 'N/A'; ?></td>
                                                    <td><?php echo htmlentities($row['productname']); ?></td>
                                                    <td><?php echo htmlentities($row['quantity']); ?></td>
                                                    <td>₹<?php echo number_format($amount, 2); ?></td>
                                                    <td><?php echo htmlentities($row['orderdate']); ?></td>
                                                    <td><?php echo $row['status'] ? htmlentities($row['status']) : 'Pending'; ?></td>
                                                    <td>
                                                        <a href="/updateorder.php?oid=<?php echo $row['id']; ?>" title="Update order">
                                                            <i class="icon-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php $cnt++; } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <div class="no-orders">
                                    <p>No pending orders found</p>
                                    <p><small>All orders may have been processed or marked as delivered.</small></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php');?>

<script src="/scripts/jquery-1.9.1.min.js"></script>
<script src="/scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="/bootstrap/js/bootstrap.min.js"></script>
<script src="/scripts/datatables/jquery.dataTables.js"></script>

<script>
    $(document).ready(function() {
        console.log("Document is ready. Initializing DataTables...");

        setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#ordersTable')) {
                $('#ordersTable').DataTable().destroy();
            }

            $('#ordersTable').DataTable({
                "scrollX": true,
                "pageLength": 25,
                "order": [[7, "desc"]], // Sort by Order Date Descending
                "autoWidth": false,
                "destroy": true
            });

            console.log("DataTables initialized successfully!");
        }, 500); // Ensures the table is fully rendered before initialization
    });
</script>
</body>
</html>
