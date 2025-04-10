<?php
// 🚫 Do not add any space or blank lines above this line!
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

// ✅ Check if admin is logged in
if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');

if (isset($_GET['del'])) {
    mysqli_query($con, "DELETE FROM products WHERE id = '" . $_GET['id'] . "'");
    $_SESSION['delmsg'] = "Product deleted !!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Admin | Manage Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">
    <link href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">
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
                            <h3>Manage Products</h3>
                        </div>
                        <div class="module-body table">

                            <?php if (isset($_SESSION['delmsg'])) { ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Oh snap!</strong> <?php echo htmlentities($_SESSION['delmsg']); ?>
                                    <?php $_SESSION['delmsg'] = ""; ?>
                                </div>
                            <?php } ?>

                            <br />

                            <table class="datatable-1 table table-bordered table-striped display" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>Company Name</th>
                                        <th>Product Creation Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($con, "
                                        SELECT 
                                            products.*, 
                                            category.categoryName, 
                                            subcategory.subcategory 
                                        FROM 
                                            products 
                                            JOIN category ON category.id = products.category 
                                            JOIN subcategory ON subcategory.id = products.subCategory
                                    ");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($row['productName'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['categoryName'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['subcategory'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['productCompany'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['postingDate'] ?? ''); ?></td>
                                            <td>
                                                <a href="edit-products.php?id=<?php echo $row['id']; ?>"><i class="icon-edit"></i></a>
                                                <a href="manage-products.php?id=<?php echo $row['id']; ?>&del=delete" onClick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a>
                                            </td>
                                        </tr>
                                    <?php $cnt++; } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php'); ?>

<!-- Scripts -->
<script src="scripts/jquery-1.9.1.min.js"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="scripts/flot/jquery.flot.js"></script>
<script src="scripts/datatables/jquery.dataTables.js"></script>
<script>
    $(document).ready(function () {
        $('.datatable-1').dataTable();
        $('.dataTables_paginate').addClass("btn-group datatable-pagination");
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
        $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
    });
</script>
</body>
</html>
