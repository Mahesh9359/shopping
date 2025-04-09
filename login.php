<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize login attempts if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_login_attempt'] = 0;
}

// Code user Registration
if(isset($_POST['submit'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    $name = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['emailid']);
    $contactno = mysqli_real_escape_string($con, $_POST['contactno']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    
    // Validation
    $errors = array();
    
    // Name validation
    if(empty($name)) {
        $errors[] = "Full name is required";
    } elseif(!preg_match("/^[a-zA-Z ]*$/", $name)) {
        $errors[] = "Only letters and white space allowed in name";
    }
    
    // Email validation
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        // Check if email already exists
        $check_email = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
        if(mysqli_num_rows($check_email) > 0) {
            $errors[] = "Email already exists";
        }
    }
    
    // Contact number validation
    if(empty($contactno)) {
        $errors[] = "Contact number is required";
    } elseif(!preg_match("/^[0-9]{10}$/", $contactno)) {
        $errors[] = "Contact number must be 10 digits";
    }
    
    // Password validation
    if(empty($password)) {
        $errors[] = "Password is required";
    } elseif(strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    } elseif(!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/", $password)) {
        $errors[] = "Password must contain at least one number, one uppercase and one lowercase letter";
    } elseif($password != $confirmpassword) {
        $errors[] = "Passwords do not match";
    }
    
    // If no errors, proceed with registration
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = mysqli_query($con, "INSERT INTO users(name,email,contactno,password) VALUES('$name','$email','$contactno','$hashed_password')");
        
        if($query) {
            echo "<script>alert('You are successfully registered');</script>";
            echo "<script>window.location.href='login.php'</script>";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
    
    // Display errors if any
    if(!empty($errors)) {
        $_SESSION['errmsg'] = implode("<br>", $errors);
        echo "<script>window.location.href='login.php'</script>";
        exit();
    }
}

// Code for User login
if(isset($_POST['login'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // Rate limiting check
    if ($_SESSION['login_attempts'] > 5 && (time() - $_SESSION['last_login_attempt']) < 300) {
        $_SESSION['errmsg'] = "Too many login attempts. Please try again in 5 minutes.";
        header("Location: login.php");
        exit();
    }

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    $errors = array();
    
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if(empty($password)) {
        $errors[] = "Password is required";
    }
    
    if(empty($errors)) {
        $query = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
        $num = mysqli_fetch_array($query);
        
        if($num > 0 && password_verify($password, $num['password'])) {
            // Reset login attempts on successful login
            $_SESSION['login_attempts'] = 0;
            
            $extra = "my-cart.php";
            $_SESSION['login'] = $email;
            $_SESSION['id'] = $num['id'];
            $_SESSION['username'] = $num['name'];
            $uip = $_SERVER['REMOTE_ADDR'];
            $status = 1;
            
            mysqli_query($con, "INSERT INTO userlog(userEmail,userip,status) VALUES('".$_SESSION['login']."','$uip','$status')");
            
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            header("location:http://$host$uri/$extra");
            exit();
        } else {
            $errors[] = "Invalid email or password";
            $_SESSION['login_attempts']++;
            $_SESSION['last_login_attempt'] = time();
        }
    }
    
    // If errors exist
    if(!empty($errors)) {
        $_SESSION['errmsg'] = implode("<br>", $errors);
        $extra = "login.php";
        $uip = $_SERVER['REMOTE_ADDR'];
        $status = 0;
        mysqli_query($con, "INSERT INTO userlog(userEmail,userip,status) VALUES('$email','$uip','$status')");
        
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("location:http://$host$uri/$extra");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">

	    <title>Shopping Portal | Signin | Signup</title>

	    <!-- Bootstrap Core CSS -->
	    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	    
	    <!-- Customizable CSS -->
	    <link rel="stylesheet" href="/assets/css/main.css">
	    <link rel="stylesheet" href="/assets/css/green.css">
	    <link rel="stylesheet" href="/assets/css/owl.carousel.css">
		<link rel="stylesheet" href="/assets/css/owl.transitions.css">
		<!--<link rel="stylesheet" href="assets/css/owl.theme.css">-->
		<link href="/assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="/assets/css/animate.min.css">
		<link rel="stylesheet" href="/assets/css/rateit.css">
		<link rel="stylesheet" href="/assets/css/bootstrap-select.min.css">

		<!-- Demo Purpose Only. Should be removed in production -->
		<link rel="stylesheet" href="/assets/css/config.css">

		<link href="/assets/css/green.css" rel="alternate stylesheet" title="Green color">
		<link href="/assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="/assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="/assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="/assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color">
		<!-- Demo Purpose Only. Should be removed in production : END -->

		
		<!-- Icons/Glyphs -->
		<link rel="stylesheet" href="/assets/css/font-awesome.min.css">

        <!-- Fonts --> 
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		
		<!-- Favicon -->
		<link rel="shortcut icon" href="/assets/images/favicon.ico">
<script type="text/javascript">
function valid() {
    if(document.register.password.value != document.register.confirmpassword.value) {
        alert("Password and Confirm Password Field do not match!!");
        document.register.confirmpassword.focus();
        return false;
    }
    return true;
}
</script>
<script>
function userAvailability() {
    $("#loaderIcon").show();
    jQuery.ajax({
        url: "check_availability.php",
        data:'email='+$("#email").val(),
        type: "POST",
        success:function(data){
            $("#user-availability-status1").html(data);
            $("#loaderIcon").hide();
        },
        error:function (){}
    });
}
</script>
	</head>
    <body class="cnt-home">
	
		<!-- ============================================== HEADER ============================================== -->
<header class="header-style-1">

	<!-- ============================================== TOP MENU ============================================== -->
<?php include('includes/top-header.php');?>
<!-- ============================================== TOP MENU : END ============================================== -->
<?php include('includes/main-header.php');?>
	<!-- ============================================== NAVBAR ============================================== -->
<?php include('includes/menu-bar.php');?>
<!-- ============================================== NAVBAR : END ============================================== -->

</header>

<!-- ============================================== HEADER : END ============================================== -->
<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="home.html">Home</a></li>
				<li class='active'>Authentication</li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb -->

<div class="body-content outer-top-bd">
	<div class="container">
		<div class="sign-in-page inner-bottom-sm">
			<div class="row">
				<!-- Sign-in -->			
<div class="col-md-6 col-sm-6 sign-in">
	<h4 class="">sign in</h4>
	<p class="">Hello, Welcome to your account.</p>
	<form class="register-form outer-top-xs" method="post">
	<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
	<span style="color:red;">
	<?php
	if(isset($_SESSION['errmsg'])) {
		echo htmlentities($_SESSION['errmsg']);
		unset($_SESSION['errmsg']);
	}
	?>
	</span>
		<div class="form-group">
		    <label class="info-title" for="exampleInputEmail1">Email Address <span>*</span></label>
		    <input type="email" name="email" class="form-control unicase-form-control text-input" id="exampleInputEmail1" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
		</div>
	  	<div class="form-group">
		    <label class="info-title" for="exampleInputPassword1">Password <span>*</span></label>
		    <input type="password" name="password" class="form-control unicase-form-control text-input" id="exampleInputPassword1" required minlength="8">
		</div>
		<div class="radio outer-xs">
		  	<a href="forgot-password.php" class="forgot-password pull-right">Forgot your Password?</a>
		</div>
	  	<button type="submit" class="btn-upper btn btn-primary checkout-page-button" name="login">Login</button>
	</form>					
</div>
<!-- Sign-in -->

<!-- create a new account -->
<div class="col-md-6 col-sm-6 create-new-account">
	<h4 class="checkout-subtitle">create a new account</h4>
	<p class="text title-tag-line">Create your own Shopping account.</p>
	<form class="register-form outer-top-xs" role="form" method="post" name="register" onSubmit="return valid();">
	<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
	<div class="form-group">
	    	<label class="info-title" for="fullname">Full Name <span>*</span></label>
	    	<input type="text" class="form-control unicase-form-control text-input" id="fullname" name="fullname" required pattern="[a-zA-Z ]+" title="Only letters and spaces allowed">
	  	</div>

		<div class="form-group">
	    	<label class="info-title" for="exampleInputEmail2">Email Address <span>*</span></label>
	    	<input type="email" class="form-control unicase-form-control text-input" id="email" onBlur="userAvailability()" name="emailid" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
	    	       <span id="user-availability-status1" style="font-size:12px;"></span>
	  	</div>

	<div class="form-group">
	    	<label class="info-title" for="contactno">Contact No. <span>*</span></label>
	    	<input type="text" class="form-control unicase-form-control text-input" id="contactno" name="contactno" maxlength="10" required pattern="[0-9]{10}" title="10 digit phone number">
	  	</div>

	<div class="form-group">
	    	<label class="info-title" for="password">Password. <span>*</span></label>
	    	<input type="password" class="form-control unicase-form-control text-input" id="password" name="password" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters">
	  	</div>

	<div class="form-group">
	    	<label class="info-title" for="confirmpassword">Confirm Password. <span>*</span></label>
	    	<input type="password" class="form-control unicase-form-control text-input" id="confirmpassword" name="confirmpassword" required>
	  	</div>

	  	<button type="submit" name="submit" class="btn-upper btn btn-primary checkout-page-button" id="submit">Sign Up</button>
	</form>
	<span class="checkout-subtitle outer-top-xs">Sign Up Today And You'll Be Able To :  </span>
	<div class="checkbox">
	  	<label class="checkbox">
		  	Speed your way through the checkout.
		</label>
		<label class="checkbox">
		Track your orders easily.
		</label>
		<label class="checkbox">
 Keep a record of all your purchases.
		</label>
	</div>
</div>	
<!-- create a new account -->			</div><!-- /.row -->
		</div>
<?php include('includes/brands-slider.php');?>
</div>
</div>
<?php include('includes/footer.php');?>
	<script src="assets/js/jquery-1.11.1.min.js"></script>
	
	<script src="assets/js/bootstrap.min.js"></script>
	
	<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>
	
	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="assets/js/jquery.rateit.min.js"></script>
    <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>

	<!-- For demo purposes – can be removed on production -->
	
	<script src="switchstylesheet/switchstylesheet.js"></script>
	
	<script>
		$(document).ready(function(){ 
			$(".changecolor").switchstylesheet( { seperator:"color"} );
			$('.show-theme-options').click(function(){
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>
	<!-- For demo purposes – can be removed on production : End -->
</body>
</html>