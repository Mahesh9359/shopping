<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
}
else{
    date_default_timezone_set('Asia/Kolkata');
    $currentTime = date('d-m-Y h:i:s A', time());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| Today's Orders</title>
    <link type="text/css" href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="/css/theme.css" rel="stylesheet">
    <link type="text/css" href="/images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <link rel="stylesheet" href="/https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/scripts/datatables/jquery.dataTables.min.js"></script>
    
    <style>
        /* Table container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 20px;
        }
        
        /* Table styling */
        .datatable-1 {
            min-width: 1200px;
            display: table !important;
        }
        
        /* Search box styling */
        .search-container {
            margin-bottom: 15px;
            float: right;
            width: 300px;
        }
        
        /* Pagination controls */
        .dataTables_wrapper .dataTables_paginate {
            float: none;
            text-align: center;
            margin-top: 20px;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 3px;
            border: 1px solid #ddd;
            background: #f8f8f8;
            color: #428bca;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #428bca;
            color: white !important;
            border-color: #357ebd;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #eee;
            text-decoration: none;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Scroll body styling */
        .dataTables_scrollBody {
            overflow-y: auto !important;
            max-height: 500px !important;
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
                                <h3>Today's Orders</h3>
                            </div>
                            <div class="module-body">
                            
                                
                                <div class="table-responsive">
                                    <table id="ordersTable" class="datatable-1 table table-bordered table-striped display">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email / Contact No</th>
                                                <th>Shipping Address</th>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Amount</th>
                                                <th>Order Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $f1="00:00:00";
                                            $from=date('Y-m-d')." ".$f1;
                                            $t1="23:59:59";
                                            $to=date('Y-m-d')." ".$t1;
                                            $query=mysqli_query($con,"SELECT users.name AS username, users.email AS useremail, users.contactno AS usercontact, users.shippingAddress AS shippingaddress, users.shippingCity AS shippingcity, users.shippingState AS shippingstate, users.shippingPincode AS shippingpincode, products.productName AS productname, products.shippingCharge AS shippingcharge, orders.quantity AS quantity, orders.orderDate AS orderdate, products.productPrice AS productprice, orders.id AS id FROM orders JOIN users ON orders.userId=users.id JOIN products ON products.id=orders.productId WHERE orders.orderDate BETWEEN '$from' AND '$to'");
                                            $cnt=1;
                                            while($row=mysqli_fetch_array($query)) {
                                            ?>
                                            <tr>
                                                <td><?php echo htmlentities($cnt);?></td>
                                                <td><?php echo htmlentities($row['username']);?></td>
                                                <td><?php echo htmlentities($row['useremail']);?>/<?php echo htmlentities($row['usercontact']);?></td>
                                                <td><?php echo htmlentities($row['shippingaddress'].",".$row['shippingcity'].",".$row['shippingstate']."-".$row['shippingpincode']);?></td>
                                                <td><?php echo htmlentities($row['productname']);?></td>
                                                <td><?php echo htmlentities($row['quantity']);?></td>
                                                <td><?php echo htmlentities(($row['quantity']*$row['productprice'])+$row['shippingcharge']);?></td>
                                                <td><?php echo htmlentities($row['orderdate']);?></td>
                                                <td><a href="/updateorder.php?oid=<?php echo htmlentities($row['id']);?>" title="Update order" target="_blank"><i class="icon-edit"></i></a></td>
                                            </tr>
                                            <?php $cnt++; } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    // Initialize DataTables with both scrollbars
    var table = $('#ordersTable').DataTable({
        "scrollX": true,
        "scrollY": "500px",
        "scrollCollapse": true,
        "paging": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "dom": '<"top"lf>rt<"bottom"ip>',
        "language": {
            "paginate": {
                "first": "First",
                "previous": "Previous",
                "next": "Next",
                "last": "Last"
            },
            "search": "_INPUT_",
            "searchPlaceholder": "Search records..."
        }
    });

    // Custom Search Functionality
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>

</body>
<?php } ?>