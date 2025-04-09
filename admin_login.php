    <?php
    session_start();
    error_reporting(0);
    include('includes/config.php');

    // Admin login logic
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']); // Hash the password

        $query = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
        $num = mysqli_fetch_array($query);

        if ($num > 0) {
            $_SESSION['admin_login'] = $username;
            header("location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['errmsg'] = "Invalid username or password";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


        <style>
            body {
                background: linear-gradient(135deg, #6e8efb, #a777e3);
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .login-container {
                max-width: 400px;
                margin: 100px auto;
                background-color: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }
            h2 {
                margin-bottom: 30px;
                font-weight: 600;
                color: #4a4a4a;
            }
            .btn-primary {
                background-color: #6e8efb;
                border: none;
            }
            .btn-primary:hover {
                background-color: #4a6efb;
            }
            .error-msg {
                color: red;
                margin-top: 10px;
            }
            .form-control:focus {
                border-color: #6e8efb;
                box-shadow: 0 0 5px rgba(110, 142, 251, 0.5);
            }
        </style>
    </head>
    <body>

    <div class="login-container">
        <h2 class="text-center">Admin Login</h2>
        <form method="post" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Enter admin username" required>
            </div>
            <div class="form-group mt-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Enter admin password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-4" name="login">Login</button>
            <?php if (!empty($_SESSION['errmsg'])): ?>
                <div class="error-msg text-center">
                    <?php echo htmlentities($_SESSION['errmsg']); ?>
                </div>
            <?php $_SESSION['errmsg'] = ""; endif; ?>
        </form>
    </div>

    </body>
    </html>
