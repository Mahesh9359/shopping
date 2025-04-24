<?php 
// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle quantity updates
if (isset($_GET['action']) && isset($_POST['quantity'])) {
    if (!empty($_SESSION['cart'])) {
        foreach ($_POST['quantity'] as $key => $val) {
            $val = intval($val); // Ensure quantity is an integer
            if ($val <= 0) {
                unset($_SESSION['cart'][$key]);
            } else {
                $_SESSION['cart'][$key]['quantity'] = $val;
            }
        }
        
        // Recalculate cart quantity total after update
        $totalqunty = 0;
        foreach ($_SESSION['cart'] as $item) {
            if (isset($item['quantity'])) {
                $totalqunty += $item['quantity'];
            }
        }
        $_SESSION['qnty'] = $totalqunty;
    }
}

?>

<div class="main-header">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-3 logo-holder">
                <!-- ============================================================= LOGO ============================================================= -->
                <div class="logo">
                    <a href="index.php">
                        <h3>ONLINE GROCERY STORE</h3>
                    </a>
                </div>        
            </div>
            
            <div class="col-xs-12 col-sm-12 col-md-6 top-search-holder">
                <div class="search-area">
                    <form name="search" method="post" action="search-result.php">
                        <div class="control-group">
                            <input class="search-field" placeholder="Search here..." name="product" required="required" />
                            <button class="search-button" type="submit" name="search"></button>    
                        </div>
                    </form>
                </div><!-- /.search-area -->
                <!-- ============================================================= SEARCH AREA : END ============================================================= -->
            </div><!-- /.top-search-holder -->

            <div class="col-xs-12 col-sm-12 col-md-3 animate-dropdown top-cart-row">
                <!-- ============================================================= SHOPPING CART DROPDOWN ============================================================= -->
                <?php
                // Calculate total quantity
                $totalqunty = 0;
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        if (isset($item['quantity'])) {
                            $totalqunty += $item['quantity'];
                        }
                    }
                    $_SESSION['qnty'] = $totalqunty;
                ?>
                
                <div class="dropdown dropdown-cart">
                    <a href="#" class="dropdown-toggle lnk-cart" data-toggle="dropdown">
                        <div class="items-cart-inner">
                            <div class="basket">
                                <i class="glyphicon glyphicon-shopping-cart"></i>
                            </div>
                            <div class="basket-item-count">
                                <span class="count"><?php echo isset($_SESSION['qnty']) ? $_SESSION['qnty'] : 0; ?></span>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        if (!empty($_SESSION['cart'])) {
                            $sql = "SELECT * FROM products WHERE id IN(";
                            $ids = array();
                            foreach($_SESSION['cart'] as $id => $value) {
                                $ids[] = intval($id);
                            }
                            $sql .= implode(",", $ids) . ") ORDER BY id ASC";
                            $query = mysqli_query($con, $sql);
                            $totalprice = 0;
                            
                            if($query && mysqli_num_rows($query) > 0) {
                                while($row = mysqli_fetch_array($query)) {
                                    if (isset($_SESSION['cart'][$row['id']]['quantity'])) {
                                        $quantity = $_SESSION['cart'][$row['id']]['quantity'];
                                        $subtotal = $quantity * ($row['productPrice'] + $row['shippingCharge']);
                                        $totalprice += $subtotal;
                        ?>
                        <li>
                            <div class="cart-item product-summary">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="image">
                                            <a href="product-details.php?pid=<?php echo $row['id'];?>">
                                                <img src="admin/productimages/<?php echo $row['id'];?>/<?php echo $row['productImage1'];?>" width="35" height="50" alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-xs-7">
                                        <h3 class="name"><a href="product-details.php?pid=<?php echo $row['id'];?>"><?php echo $row['productName']; ?></a></h3>
                                        <div class="price">Rs.<?php echo ($row['productPrice'] + $row['shippingCharge']); ?>*<?php echo $quantity; ?></div>
                                    </div>
                                </div>
                            </div><!-- /.cart-item -->
                        <?php 
                                    }
                                }
                            }
                        ?>
                        <div class="clearfix"></div>
                        <hr>
                        <div class="clearfix cart-total">
                            <div class="pull-right">
                                <span class="text">Total :</span><span class='price'>Rs.<?php echo number_format($totalprice, 2); ?></span>
                            </div>
                            <div class="clearfix"></div>
                            <a href="my-cart.php" class="btn btn-upper btn-primary btn-block m-t-20">My Cart</a>    
                        </div><!-- /.cart-total-->
                        <?php } ?>
                    </ul><!-- /.dropdown-menu-->
                </div><!-- /.dropdown-cart -->
                <?php } else { ?>
                <div class="dropdown dropdown-cart">
                    <a href="#" class="dropdown-toggle lnk-cart" data-toggle="dropdown">
                        <div class="items-cart-inner">
                            <div class="basket">
                                <i class="glyphicon glyphicon-shopping-cart"></i>
                            </div>
                            <div class="basket-item-count"><span class="count">0</span></div>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="cart-item product-summary">
                                <div class="row">
                                    <div class="col-xs-12">
                                        Your Shopping Cart is Empty.
                                    </div>
                                </div>
                            </div><!-- /.cart-item -->
                            <hr>
                            <div class="clearfix cart-total">
                                <div class="clearfix"></div>
                                <a href="index.php" class="btn btn-upper btn-primary btn-block m-t-20">Continue Shopping</a>    
                            </div><!-- /.cart-total-->
                        </li>
                    </ul><!-- /.dropdown-menu-->
                </div>
                <?php } ?>
                <!-- ============================================================= SHOPPING CART DROPDOWN : END============================================================= -->
            </div><!-- /.top-cart-row -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div>