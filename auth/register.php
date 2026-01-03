<?php
include("../config/db.php");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];
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

/* Background glow — SAME AS LOGIN */
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

/* Register Card */
.register-box{
    width:420px;
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

.register-box h2{
    font-family:'Parisienne',cursive;
    font-size:42px;
    color:#ffbb55; /* SAME AS LOGIN */
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

input,select{
    width:100%;
    padding:12px;
    margin-bottom:18px;
    border-radius:10px;
    border:none;
    background:rgba(255,255,255,0.15);
    color:#fff;
    font-size:14px;
}

input:focus,select:focus{
    outline:none;
    background:rgba(255,255,255,0.22);
    box-shadow:0 0 8px #ff9900;
}

select option{
    background:#111;
    color:#fff;
}

button{
    width:100%;
    padding:12px;
    border-radius:10px;
    background:#ff9900; /* SAME BUTTON COLOR */
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

.msg{
    color:#ff6363;
    font-weight:600;
    margin-bottom:12px;
}

.login-link{
    margin-top:14px;
    display:block;
    color:#ffcc66; /* SAME LINK COLOR */
    text-decoration:none;
}
.login-link:hover{text-decoration:underline}
</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <img src="../uploads/images/logo.png">
</div>

<!-- Register Box -->
<div class="register-box">
    <h2>Create Account</h2>

    <?php if($msg!=""){ ?>
        <div class="msg"><?= $msg ?></div>
    <?php } ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter full name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter email" required>

        <label>Role</label>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="organizer">Organizer</option>
            <option value="admin">Admin</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" placeholder="Create password" required>

        <button type="submit">Register</button>
    </form>

    <a href="login.php" class="login-link">
        Already registered? Login
    </a>
</div>

</body>
</html>
