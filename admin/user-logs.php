<?php
// 🚫 Absolutely nothing above this line
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

// ✅ Session check
if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Admin | Users Log</title>
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
                            <h3>User Login Logs</h3>
                        </div>
                        <div class="module-body table">
                            <table class="datatable-1 table table-bordered table-striped display" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User Email</th>
                                        <th>User IP</th>
                                        <th>Login Time</th>
                                        <th>Logout Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM userlog");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($row['userEmail'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['userip'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['loginTime'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['logout'] ?? ''); ?></td>
                                            <td>
                                                <?php echo ($row['status'] == 1) ? 'Successful' : 'Failed'; ?>
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
