<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
/* NAVBAR */
.navbar{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    padding:20px 60px;
    display:grid;
    grid-template-columns: auto 1fr auto;
    align-items:center;
    z-index:100;
}

/* LOGO */
.logo{
    font-size:22px;
    font-weight:700;
    letter-spacing:1px;
    color:#fff;
}

/* CENTER MENU */
.menu{
    text-align:center;
}

.menu a{
    margin:0 18px;
    color:#ddd;
    text-decoration:none;
    font-weight:500;
}

.menu a:hover{
    color:#ff7a18;
}

/* RIGHT ACTION */
.actions a{
    padding:10px 22px;
    border-radius:25px;
    text-decoration:none;
    font-weight:600;
}

/* LOGIN BUTTON */
.login-btn{
    color:#fff;
    border:1px solid rgba(255,255,255,0.4);
    backdrop-filter: blur(8px);
}

.login-btn:hover{
    background:rgba(255,255,255,0.1);
}

/* LOGOUT BUTTON */
.logout-btn{
    background:rgba(255,255,255,0.15);
    color:#fff;
}

/* MOBILE */
@media(max-width:768px){
    .navbar{
        grid-template-columns: 1fr;
        text-align:center;
        gap:15px;
    }
}
</style>

<div class="navbar">

    <!-- LEFT -->
    <div class="logo">EventHub</div>

    <!-- CENTER -->
    <div class="menu">
        <a href="index.php">Home</a>
        <a href="events.php">Events</a>
        <a href="about.php">About</a>
    </div>

    <!-- RIGHT -->
    <div class="actions">
        <?php if(!isset($_SESSION['role'])){ ?>
            <a href="auth/login.php" class="login-btn">Login</a>
        <?php } else { ?>
            <a href="logout.php" class="logout-btn">Logout</a>
        <?php } ?>
    </div>

</div>
