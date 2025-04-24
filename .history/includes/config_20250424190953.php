<?php
// Database connection only
define('DB_SERVER', 'shuttle.proxy.rlwy.net');
define('DB_USER', 'root');
define('DB_PASS', 'iBBQXBVUlxpctlJzhpnVFWjOYeCAzvPa');
define('DB_NAME', 'railway');
define('DB_PORT', 57055);


$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the password

    $stmt = $con->prepare("SELECT * FROM admin WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $num = $result->fetch_array();

    if ($num > 0) {
        // Successful login logic
        $_SESSION['admin_login'] = $username;
        header("location: admin_dashboard.php"); // Redirect to the admin dashboard
        exit();
    } else {
        echo "<script>alert('Invalid username or password');</script>";
    }
}
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

?>
