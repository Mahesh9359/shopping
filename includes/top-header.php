<?php 
session_start(); // Ensure session is started
?>

<div class="top-bar animate-dropdown">
    <div class="container">
        <div class="header-top-inner">
            <div class="cnt-account">
                <ul class="list-unstyled">

                <?php if (isset($_SESSION['login']) && strlen($_SESSION['login']) > 0) { ?>
                    <li><a href="/"><i class="icon fa fa-user"></i>Welcome - <?php echo htmlentities($_SESSION['username']); ?></a></li>
                <?php } ?>

                <li><a href="my-account.php"><i class="icon fa fa-user"></i>My Account</a></li>
                <li><a href="my-wishlist.php"><i class="icon fa fa-heart"></i>Wishlist</a></li>
                <li><a href="my-cart.php"><i class="icon fa fa-shopping-cart"></i>My Cart</a></li>

                <?php if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) { ?>
                    <li><a href="login.php"><i class="icon fa fa-sign-in"></i>Login</a></li>
                <?php } else { ?>
                    <li><a href="#" id="logout-btn"><i class="icon fa fa-sign-out"></i>Logout</a></li>

                <?php } ?>	
                </ul>
            </div><!-- /.cnt-account -->

            <div class="cnt-block">
                <ul class="list-unstyled list-inline">
                    <li class="dropdown dropdown-small">
                        <a href="track-orders.php" class="dropdown-toggle"><span class="key">Track Order</span></a>
                        <a href="admin">Admin Login</a>
                    </li>
                </ul>
            </div>

            <div class="clearfix"></div>
        </div><!-- /.header-top-inner -->
    </div><!-- /.container -->
</div><!-- /.header-top -->
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        });
    }
});
</script>
