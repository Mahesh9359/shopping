<?php
session_start();
error_reporting(0);
include('includes/config.php'); // Ensure this file connects to your database

// Admin login logic
if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the password

    // Query to check admin credentials
    $query = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    $num = mysqli_fetch_array($query);

    if($num > 0) {
        $_SESSION['admin_login'] = $username; // Store session variable
        header("location: admin_dashboard.php"); // Redirect to admin dashboard
        exit();
    } else {
        $_SESSION['errmsg'] = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <img src="assets/img/logo.png" alt="YourBasket Admin" class="img-fluid mx-auto d-block mb-4" style="max-height: 60px;">

    <style>
    body {
        background: linear-gradient(to right, #00c6ff, #0072ff);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-container {
        background: #ffffff;
        padding: 40px 30px;
        border-radius: 20px;
        max-width: 400px;
        width: 100%;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .login-container:hover {
        transform: scale(1.02);
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.25);
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
        font-weight: 700;
        font-size: 28px;
    }

    label {
        font-weight: 600;
        color: #555;
    }

    .form-control {
        border-radius: 10px;
        padding: 10px 12px;
        border: 1px solid #ddd;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: #0072ff;
        box-shadow: 0 0 0 0.2rem rgba(0,114,255,.25);
    }

    .btn-primary {
        background: #0072ff;
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background: #005ecb;
    }

    .error-msg {
        color: #ff4d4f;
        font-size: 14px;
        margin-top: 15px;
        text-align: center;
        font-weight: bold;
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 30px 20px;
        }
    }
</style>

</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="login">Login</button>
            <span style="color:red;">
                <?php echo htmlentities($_SESSION['errmsg']); ?>
                <?php $_SESSION['errmsg'] = ""; ?>
            </span>
        </form>
    </div>
</body>
</html>
