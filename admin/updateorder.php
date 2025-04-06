<?php
session_start();
include_once 'include/config.php';
if(strlen($_SESSION['alogin'])==0) { 
    header('location:index.php');
} else {
    $oid = intval($_GET['oid']);
    if(isset($_POST['submit2'])){
        $status = $_POST['status'];
        $remark = $_POST['remark'];
        
        $query = mysqli_query($con,"INSERT INTO ordertrackhistory(orderId,status,remark) VALUES('$oid','$status','$remark')");
        $sql = mysqli_query($con,"UPDATE orders SET orderStatus='$status' WHERE id='$oid'");
        echo "<script>alert('Order updated successfully...');</script>";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| Update Order</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <script language="javascript" type="text/javascript">
        var popUpWin=0;
        function popUpWindow(URLStr, left, top, width, height) {
            if(popUpWin) {
                if(!popUpWin.closed) popUpWin.close();
            }
            popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+600+',height='+600+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
        }
    </script>
    <style>
        .fontkink1 {
            font-weight: bold;
            padding-right: 10px;
        }
        .fontkink {
            color: #333;
        }
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
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
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
                                <h3>Update Order #<?php echo $oid; ?></h3>
                            </div>
                            <div class="module-body">
                                <div class="status-form">
                                    <form name="updateticket" id="updateticket" method="post"> 
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr height="50">
                                                <td colspan="2" style="padding-left:0px;">
                                                    <div style="font-size:16px;font-weight:bold;color:#0066cc;"> <b>Update Order Details</b></div>
                                                </td>
                                            </tr>
                                            <tr height="30">
                                                <td class="fontkink1"><b>Order Id:</b></td>
                                                <td class="fontkink"><?php echo $oid;?></td>
                                            </tr>
                                            <?php 
                                            $ret = mysqli_query($con,"SELECT * FROM ordertrackhistory WHERE orderId='$oid'");
                                            while($row=mysqli_fetch_array($ret)) {
                                            ?>
          
                                            <tr height="20">
                                                <td class="fontkink1"><b>Status:</b></td>
                                                <td class="fontkink"><?php echo $row['status'];?></td>
                                            </tr>
                                            <tr height="20">
                                                <td class="fontkink1"><b>Remark:</b></td>
                                                <td class="fontkink"><?php echo $row['remark'];?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><hr /></td>
                                            </tr>
                                            <?php } ?>
                                            
                                            <?php 
                                            $st='Delivered';
                                            $rt = mysqli_query($con,"SELECT * FROM orders WHERE id='$oid'");
                                            while($num=mysqli_fetch_array($rt)) {
                                                $currrentSt=$num['orderStatus'];
                                            }
                                            if($st==$currrentSt) { 
                                            ?>
                                            <tr>
                                                <td colspan="2"><b>Product Delivered</b></td>
                                            </tr>
                                            <?php } else { ?>
                                              <tr height="50">
    <td class="fontkink1">Status: </td>
    <td class="fontkink">
        <div class="control-group">
            <div class="controls">
                <select name="status" class="form-control span4" required="required">
                    <option value="">Select Status</option>
                    <option value="in Process">In Process</option>
                    <option value="Delivered">Delivered</option>
                </select>
            </div>
        </div>
    </td>
</tr>
                                            <tr style=''>
                                                <td class="fontkink1">Remark:</td>
                                                <td class="fontkink" align="justify">
                                                    <textarea cols="50" rows="7" name="remark" required="required"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fontkink1">&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td class="fontkink"></td>
                                                <td class="fontkink">
                                                    <input type="submit" name="submit2" value="Update" class="btn btn-primary" style="cursor: pointer;" /> &nbsp;&nbsp;   
                                                    <input name="Submit2" type="button" class="btn btn-danger" value="Close this Window" onClick="window.close();" style="cursor: pointer;" />
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </form>
                                </div>
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
        function f2() {
            window.close();
        }
        function f3() {
            window.print(); 
        }
    </script>
</body>
</html>
<?php } ?>