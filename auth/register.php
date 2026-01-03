<?php
include("../config/db.php");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = md5($_POST['password']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $msg = "Email already exists";
    } else {

        $query = "INSERT INTO users (name,email,password,role)
                  VALUES ('$name','$email','$password','$role')";

        if (mysqli_query($conn, $query)) {
            header("Location: login.php");
            exit();
        } else {
            $msg = "Registration failed";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EventHub | Register</title>
    <style>
        body{font-family:Arial;background:#f4f6f9}
        .box{width:450px;margin:80px auto;padding:25px;background:#fff;border-radius:8px}
        input,select,button{width:100%;padding:10px;margin:8px 0}
        button{background:#28a745;color:#fff;border:none}
        .msg{color:red;text-align:center}
    </style>
</head>
<body>

<div class="box">
    <h2 align="center">EventHub Registration</h2>

    <?php if($msg!=""){ ?>
        <p class="msg"><?= $msg ?></p>
    <?php } ?>

    <form method="POST">

        <input type="text" name="name" placeholder="Full Name" required>

        <input type="email" name="email" placeholder="Email" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="organizer">Organizer</option>
            <option value="admin">Admin</option>
        </select>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Register</button>
    </form>

    <p align="center">
        Already registered?
        <a href="login.php">Login</a>
    </p>
</div>

</body>
</html>
