<?php
session_start();
error_reporting(0);
include('includes/config.php');
$cid = intval($_GET['cid']);

// Add to cart functionality
if(isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']);
    if(isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $sql_p = "SELECT * FROM products WHERE id = {$id}";
        $query_p = mysqli_query($con, $sql_p);
        if(mysqli_num_rows($query_p) != 0) {
            $row_p = mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']] = array(
                "quantity" => 1, 
                "price" => $row_p['productPrice']
            );
            echo "<script>alert('Product has been added to the cart')</script>";
            echo "<script>document.location ='my-cart.php';</script>";
        } else {
            $message = "Product ID is invalid";
        }
    }
}

// Wishlist functionality
if(isset($_GET['pid']) && $_GET['action'] == "wishlist") {
    if(strlen($_SESSION['login']) == 0) {   
        header('location:login.php');
    } else {
        mysqli_query($con, "INSERT INTO wishlist(userId, productId) VALUES('".$_SESSION['id']."','".$_GET['pid']."')");
        echo "<script>alert('Product added to wishlist');</script>";
        header('location:my-wishlist.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="MediaCenter, Template, eCommerce">
    <meta name="robots" content="all">
    <title>Product Category</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css">
    <link href="assets/css/lightbox.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/rateit.css">
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    
    <style>
        /* Fix for card alignment */
        .category-product .row {
            display: flex;
            flex-wrap: wrap;
        }
        .category-product .col-sm-6.col-md-4 {
            display: flex;
            margin-bottom: 30px;
            padding: 0 10px; /* Added spacing between cards */
        }
        .category-product .products {
            width: 100%;
            padding: 15px; /* Added inner padding */
        }
        .product-image .image {
            height: 200px; /* Reduced image height */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px; /* Added space below image */
        }
        .product-image img {
            max-height: 100%;
            max-width: 100%;
            width: auto;
        }
        .product-info {
            min-height: 120px; /* Ensure consistent height for product info */
        }
        .product-info .name {
            font-size: 14px; /* Slightly smaller product names */
            margin-top: 45px;
			margin-bottom: 5px;
            height: 36px; /* Fixed height for product names */
            overflow: hidden;
        }
        
        .category-carousel .item {
            display: flex;
            align-items: center; /* Vertical centering */
            min-height: 100px; /* Set a minimum height */
        }
        .category-carousel .container-fluid {
            width: 100%;
        }
        .category-carousel .caption {
            padding: 20px 0; /* Add some vertical padding */
        }
        .category-carousel .big-text {
            font-size: 54px;
            margin: 0; 
			font-weight:600;
            text-align: center; 
            width: 100%;
        }
    </style>
</head>
<body class="cnt-home">
    <header class="header-style-1">
        <?php include('includes/top-header.php');?>
        <?php include('includes/main-header.php');?>
        <?php include('includes/menu-bar.php');?>
    </header>

    <div class="body-content outer-top-xs">
        <div class='container'>
            <div class='row outer-bottom-sm'>
                <div class='col-md-3 sidebar'>
                    <!-- ================================== TOP NAVIGATION ================================== -->
                    <div class="side-menu animate-dropdown outer-bottom-xs">       
                        <div class="head"><i class="icon fa fa-align-justify fa-fw"></i>Sub Categories</div>        
                        <nav class="yamm megamenu-horizontal" role="navigation">
                            <ul class="nav">
                                <?php 
                                $sql = mysqli_query($con, "SELECT id, subcategory FROM subcategory WHERE categoryid = '$cid'");
                                while($row = mysqli_fetch_array($sql)) {
                                ?>
                                <li class="dropdown menu-item">
                                    <a href="sub-category.php?scid=<?php echo $row['id'];?>" class="dropdown-toggle"><i class="icon fa fa-desktop fa-fw"></i>
                                    <?php echo $row['subcategory'];?></a>
                                </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                    
                    <!-- ================================== SHOP BY SECTION ================================== -->
                    <div class="sidebar-module-container">
                        <h3 class="section-title">shop by</h3>
                        <div class="sidebar-filter">
                            <!-- ================================== CATEGORY WIDGET ================================== -->
                            <div class="sidebar-widget wow fadeInUp outer-bottom-xs">
                                <div class="widget-header m-t-20">
                                    <h4 class="widget-title">Category</h4>
                                </div>
                                <div class="sidebar-widget-body m-t-10">
                                    <?php 
                                    $sql = mysqli_query($con, "SELECT id, categoryName FROM category");
                                    while($row = mysqli_fetch_array($sql)) {
                                    ?>
                                    <div class="accordion">
                                        <div class="accordion-group">
                                            <div class="accordion-heading">
                                                <a href="category.php?cid=<?php echo $row['id'];?>" class="accordion-toggle collapsed">
                                                    <?php echo $row['categoryName'];?>
                                                </a>
                                            </div>  
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- ================================== CATEGORY WIDGET : END ================================== -->
                        </div>
                    </div>
                    <!-- ================================== SHOP BY SECTION : END ================================== -->
                </div>
                
                <div class='col-md-9'>
                    <!-- Category Title - Now smaller -->
                    <div class="category-carousel hidden-xs">
                        <div class="item">    
                            <div class="container-fluid">
                                <div class="vertical-top text-center">
                                    <?php 
                                    $sql = mysqli_query($con, "SELECT categoryName FROM category WHERE id = '$cid'");
                                    while($row = mysqli_fetch_array($sql)) {
                                    ?>
                                    <div class="big-text">
                                        <?php echo htmlentities($row['categoryName']);?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="search-result-container">
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active" id="grid-container">
                                <div class="category-product inner-top-vs">
                                    <div class="row">
                                        <?php
                                        $ret = mysqli_query($con, "SELECT * FROM products WHERE category = '$cid'");
                                        $num = mysqli_num_rows($ret);
                                        if($num > 0) {
                                            while ($row = mysqli_fetch_array($ret)) {
                                        ?>
                                        <div class="col-sm-6 col-md-4">
                                            <div class="products">                
                                                <div class="product">        
                                                    <div class="product-image">
                                                        <div class="image">
                                                            <a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>">
                                                                <img src="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" alt="<?php echo htmlentities($row['productName']);?>">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="product-info text-left">
                                                        <h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
                                                        <div class="rating rateit-small"></div>
                                                        <div class="description"></div>

                                                        <div class="product-price">    
                                                            <span class="price">
                                                                Rs. <?php echo htmlentities($row['productPrice']);?>
                                                            </span>
                                                            <?php if($row['productPriceBeforeDiscount'] > 0) { ?>
                                                            <span class="price-before-discount">Rs. <?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="cart clearfix animate-effect">
                                                        <div class="action">
                                                            <ul class="list-unstyled">
                                                                <li class="add-cart-button btn-group">
                                                                    <?php if($row['productAvailability'] == 'In Stock'){ ?>
                                                                    <button class="btn btn-primary icon" data-toggle="dropdown" type="button">
                                                                        <i class="fa fa-shopping-cart"></i>                                                    
                                                                    </button>
                                                                    <a href="category.php?page=product&action=add&id=<?php echo $row['id']; ?>">
                                                                        <button class="btn btn-primary" type="button">Add to cart</button>
                                                                    </a>
                                                                    <?php } else { ?>
                                                                    <div class="action" style="color:red">Out of Stock</div>
                                                                    <?php } ?>
                                                                </li>
                                                                <li class="lnk wishlist">
                                                                    <a class="add-to-cart" href="category.php?pid=<?php echo htmlentities($row['id'])?>&&action=wishlist" title="Wishlist">
                                                                        <i class="icon fa fa-heart"></i>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            } 
                                        } else { 
                                        ?>
                                        <div class="col-sm-12">
                                            <h3>No Product Found</h3>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/brands-slider.php');?>
        </div>
    </div>

    <?php include('includes/footer.php');?>

    <!-- JavaScript -->
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/echo.min.js"></script>
    <script src="assets/js/jquery.easing-1.3.min.js"></script>
    <script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="assets/js/jquery.rateit.min.js"></script>
    <script src="assets/js/lightbox.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>