<?php
session_start();
include('include/config.php');

if (!isset($_SESSION['alogin']) || strlen((string)$_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// Initialize session messages
$_SESSION['msg'] = $_SESSION['msg'] ?? '';
$_SESSION['delmsg'] = $_SESSION['delmsg'] ?? '';

// Handle form submission
if(isset($_POST['submit'])) {
    $category = mysqli_real_escape_string($con, $_POST['category'] ?? '');
    $subcat = mysqli_real_escape_string($con, $_POST['subcategory'] ?? '');
    $sql = mysqli_query($con, "INSERT INTO subcategory(categoryid, subcategory) VALUES('$category','$subcat')");
    $_SESSION['msg'] = "SubCategory Created !!";
}

// Handle deletion
if(isset($_GET['del'])) {
    $id = (int)($_GET['id'] ?? 0);
    if($id > 0) {
        mysqli_query($con, "DELETE FROM subcategory WHERE id = '$id'");
        $_SESSION['delmsg'] = "SubCategory deleted !!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| SubCategory</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
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
                            <h3>Sub Category</h3>
                        </div>
                        <div class="module-body">
                            <?php if(!empty($_SESSION['msg'])): ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Well done!</strong> <?php echo htmlentities((string)$_SESSION['msg']); ?>
                                    <?php $_SESSION['msg'] = ''; ?>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($_SESSION['delmsg'])): ?>
                                <div class="alert alert-error">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Oh snap!</strong> <?php echo htmlentities((string)$_SESSION['delmsg']); ?>
                                    <?php $_SESSION['delmsg'] = ''; ?>
                                </div>
                            <?php endif; ?>

                            <br />

                            <form class="form-horizontal row-fluid" name="subcategory" method="post">
                                <div class="control-group">
                                    <label class="control-label" for="basicinput">Category</label>
                                    <div class="controls">
                                        <select name="category" class="span8 tip" required>
                                            <option value="">Select Category</option> 
                                            <?php 
                                            $query = mysqli_query($con, "SELECT * FROM category");
                                            while($row = mysqli_fetch_array($query)): ?>
                                                <option value="<?php echo htmlentities($row['id'] ?? ''); ?>">
                                                    <?php echo htmlentities($row['categoryName'] ?? ''); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="control-group">
                                    <label class="control-label" for="basicinput">SubCategory Name</label>
                                    <div class="controls">
                                        <input type="text" placeholder="Enter SubCategory Name" name="subcategory" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="controls">
                                        <button type="submit" name="submit" class="btn">Create</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="module">
                        <div class="module-head">
                            <h3>Sub Category</h3>
                        </div>
                        <div class="module-body table">
                            <table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped display" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>Creation date</th>
                                        <th>Last Updated</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $query = mysqli_query($con, "SELECT subcategory.id, category.categoryName, subcategory.subcategory, subcategory.creationDate, subcategory.updationDate FROM subcategory JOIN category ON category.id = subcategory.categoryid");
                                    $cnt = 1;
                                    while($row = mysqli_fetch_array($query)): ?>                                    
                                        <tr>
                                            <td><?php echo htmlentities((string)($cnt ?? '')); ?></td>
                                            <td><?php echo htmlentities($row['categoryName'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['subcategory'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['creationDate'] ?? ''); ?></td>
                                            <td><?php echo htmlentities($row['updationDate'] ?? ''); ?></td>
                                            <td>
                                                <a href="edit-subcategory.php?id=<?php echo htmlentities($row['id'] ?? ''); ?>"><i class="icon-edit"></i></a>
                                                <a href="subcategory.php?id=<?php echo htmlentities($row['id'] ?? ''); ?>&del=delete" onClick="return confirm('Are you sure you want to delete?')"><i class="icon-remove-sign"></i></a>
                                            </td>
                                        </tr>
                                    <?php $cnt++; endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php');?>

<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
<script src="scripts/datatables/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $('.datatable-1').dataTable();
        $('.dataTables_paginate').addClass("btn-group datatable-pagination");
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
        $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
    });
</script>
</body>
</html>