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
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

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

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}

body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
    position:relative;
}

/* Background glow */
body::before,body::after{
    content:"";
    position:absolute;
    width:380px;
    height:380px;
    border-radius:50%;
    filter:blur(100px);
    opacity:0.35;
    animation:float 8s infinite alternate ease-in-out;
}
body::before{background:#ff9900;top:-80px;left:-50px;}
body::after{background:#ff5500;bottom:-80px;right:-50px;}

@keyframes float{
    from{transform:translateY(0)}
    to{transform:translateY(40px)}
}

/* Navbar */
.navbar{
    position:fixed;
    top:0;
    width:100%;
    background:rgba(0,0,0,0.88);
    padding:10px 25px;
    border-bottom:2px solid #ff9900;
    z-index:1000;
}
.navbar img{
    height:45px;
    border-radius:6px;
}

/* Login Card */
.login-box{
    width:380px;
    background:rgba(255,255,255,0.06);
    padding:45px 35px;
    border-radius:18px;
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.15);
    box-shadow:0 15px 35px rgba(0,0,0,0.4);
    text-align:center;
    margin-top:40px;
    animation:fadeIn .6s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(20px)}
    to{opacity:1;transform:translateY(0)}
}

.login-box h2{
    font-family:'Parisienne',cursive;
    font-size:42px;
    color:#ffbb55;
    margin-bottom:25px;
    text-shadow:0 0 10px rgba(255,170,70,0.5);
}

label{
    display:block;
    text-align:left;
    color:#ddd;
    margin-bottom:6px;
    font-size:14px;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:18px;
    border-radius:10px;
    border:none;
    background:rgba(255,255,255,0.15);
    color:#fff;
    font-size:14px;
}

input:focus{
    outline:none;
    background:rgba(255,255,255,0.22);
    box-shadow:0 0 8px #ff9900;
}

button{
    width:100%;
    padding:12px;
    border-radius:10px;
    background:#ff9900;
    border:none;
    color:#000;
    font-size:17px;
    font-weight:600;
    transition:0.3s;
}

button:hover{
    background:#ff7700;
    transform:translateY(-2px);
}

.error{
    color:#ff6363;
    font-weight:600;
    margin-bottom:12px;
}

.signup-link{
    margin-top:14px;
    display:block;
    color:#ffcc66;
    text-decoration:none;
}
.signup-link:hover{text-decoration:underline}
</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <img src="../uploads/images/logo.png">
</div>

<!-- Login Box -->
<div class="login-box">
    <h2>EventHub Login</h2>

    <?php if($error!=""){ ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit">Login</button>
    </form>

    <a href="register.php" class="signup-link">
        Don't have an account? Register
    </a>
</div>

</body>
</html>
