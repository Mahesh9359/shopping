<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Admin login logic
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    $num = mysqli_fetch_array($query);

    if ($num > 0) {
        $_SESSION['admin_login'] = $username;
        header("location: /admin");
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #f5f7fa, #eaeaea);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #fff;
            border-radius: 15px;
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-login {
            background: #007bff;
            border: none;
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-login:hover {
            background: #0056b3;
        }

        .error-msg {
            color: #dc3545;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }

        .back-btn {
            margin-top: 15px;
            display: block;
            text-align: center;
            font-size: 14px;
            text-decoration: none;
            color: #007bff;
        }

        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Admin Login</h2>
    <form method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-login w-100" name="login">Login</button>

        <?php if (!empty($_SESSION['errmsg'])): ?>
            <div class="error-msg"><?php echo htmlentities($_SESSION['errmsg']); ?></div>
            <?php $_SESSION['errmsg'] = ""; ?>
        <?php endif; ?>
    </form>

    <a href="index.php" class="back-btn">← Back to Home</a>
</div>

</body>
</html>
