<?php
session_start();
include("../config/db.php");

/* ADMIN SECURITY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* VALIDATE USER ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user.");
}

$user_id = (int)$_GET['id'];

/* FETCH USER (NO PASSWORD SHOWN) */
$res = mysqli_query(
    $conn,
    "SELECT user_id, name, email, role 
     FROM users 
     WHERE user_id = $user_id 
     LIMIT 1"
);

if (!$res || mysqli_num_rows($res) !== 1) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($res);

/* UPDATE USER */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Update without changing password
    $query = "
        UPDATE users SET 
            name  = '$name',
            email = '$email'
        WHERE user_id = $user_id
    ";

    if (mysqli_query($conn, $query)) {
        header("Location: manage_users.php?msg=updated");
        exit();
    } else {
        $error = "Update failed. Try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit User | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}

body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:#0d0d0d;
    color:#fff;
    padding-top:100px;
}

/* FORM */
.form-container{
    width:430px;
    margin:40px auto;
    padding:35px;
    border-radius:16px;
    background:rgba(255,255,255,0.07);
    border:1px solid rgba(255,255,255,0.15);
    box-shadow:0 8px 26px rgba(0,0,0,0.45);
}

.form-container h1{
    text-align:center;
    font-size:36px;
    font-family:'Parisienne',cursive;
    color:#ffcc66;
    text-shadow:0 0 8px rgba(255,153,0,0.6);
    margin-bottom:25px;
}

label{
    margin-top:15px;
    color:#ffcc88;
    font-weight:500;
    font-size:15px;
    display:block;
}

input{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:8px;
    border:1px solid rgba(255,255,255,0.3);
    background:rgba(255,255,255,0.12);
    color:#fff;
    font-size:15px;
}

.readonly{
    opacity:0.7;
    cursor:not-allowed;
}

.btn{
    width:100%;
    padding:12px;
    margin-top:28px;
    background:#ff9900;
    border:none;
    border-radius:8px;
    color:#111;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
}
.btn:hover{background:#ffaa22;}

.back-btn{
    text-align:center;
    display:block;
    margin-top:18px;
    color:#ff9900;
    text-decoration:none;
}
.back-btn:hover{text-decoration:underline;}

.error{
    text-align:center;
    margin-bottom:10px;
    color:#ff6b6b;
}
</style>
</head>

<body>

<?php include(__DIR__ . "/../templates/navbar.php"); ?>

<div class="form-container">
    <h1>Edit User</h1>

    <?php if(isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">

        <label>Name</label>
        <input type="text" name="name"
               value="<?= htmlspecialchars($user['name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Role</label>
        <input type="text"
               value="<?= ucfirst($user['role']) ?>"
               class="readonly" readonly>

        <button type="submit" class="btn">Update User</button>
    </form>

    <a href="manage_users.php" class="back-btn">← Back to Manage Users</a>
</div>

</body>
</html>
