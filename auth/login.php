<?php
session_start();
include("../config/db.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && md5($password) == $user['password']) {

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: ../admin/admin_home.php");
        } elseif ($user['role'] == 'organizer') {
            header("Location: ../organizer/organizer_home.php");
        } else {
            header("Location: ../student/student_home.php");
        }
        exit();

    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EventHub | Login</title>
    <style>
        body{font-family:Arial;background:#f4f6f9}
        .box{width:400px;margin:100px auto;padding:25px;background:#fff;border-radius:8px}
        input,select,button{width:100%;padding:10px;margin:8px 0}
        button{background:#007bff;color:#fff;border:none}
        .error{color:red;text-align:center}
    </style>
</head>
<body>

<div class="box">
    <h2 align="center">EventHub Login</h2>

    <?php if($error!=""){ ?>
        <p class="error"><?= $error ?></p>
    <?php } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <p align="center">
        Don't have an account?
        <a href="register.php">Register</a>
    </p>
</div>

</body>
</html>
