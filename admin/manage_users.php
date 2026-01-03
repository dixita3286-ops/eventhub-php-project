<?php
session_start();
include("../config/db.php");

/* ADMIN SECURITY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* DELETE USER */
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];

    $check = mysqli_query(
        $conn,
        "SELECT role FROM users WHERE user_id = $uid LIMIT 1"
    );

    if ($check && mysqli_num_rows($check) === 1) {
        $user = mysqli_fetch_assoc($check);

        // Prevent deleting admin
        if ($user['role'] === 'admin') {
            header("Location: manage_users.php?err=admin");
            exit();
        }

        // Delete registrations only if student
        if ($user['role'] === 'student') {
            mysqli_query(
                $conn,
                "DELETE FROM registrations WHERE student_id = $uid"
            );
        }

        // Delete user
        mysqli_query(
            $conn,
            "DELETE FROM users WHERE user_id = $uid"
        );

        header("Location: manage_users.php?msg=deleted");
        exit();
    }
}

/* FETCH USERS — SEPARATE & CORRECT QUERIES */
$students = mysqli_query(
    $conn,
    "SELECT user_id, name, email 
     FROM users 
     WHERE role = 'student' 
     ORDER BY name"
);

$organizers = mysqli_query(
    $conn,
    "SELECT user_id, name, email 
     FROM users 
     WHERE role = 'organizer' 
     ORDER BY name"
);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Users | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Poppins,sans-serif}
body{background:#0d0d0d;color:#fff}

.main{
    padding:120px 30px 50px;
    max-width:1200px;
    margin:auto;
}

h1{
    text-align:center;
    font-size:42px;
    color:#ffcc66;
    margin-bottom:30px;
}

/* alerts */
.alert{
    padding:12px;
    border-radius:8px;
    text-align:center;
    margin-bottom:20px;
    font-weight:600;
}
.success{background:#1f4f1f;color:#b7ffb7}
.error{background:#4f1f1f;color:#ffb7b7}

/* tabs */
.tabs{
    display:flex;
    justify-content:center;
    gap:12px;
    margin-bottom:30px;
}
.tabs button{
    padding:10px 25px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    background:#222;
    color:#fff;
}
.tabs button.active{
    background:#ff9900;
    color:#000;
    font-weight:600;
}

/* cards */
.list{
    display:flex;
    flex-direction:column;
    gap:18px;
}
.card{
    background:#141414;
    border-radius:14px;
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 8px 25px rgba(0,0,0,.4);
}
.info{
    line-height:1.6;
}
.info b{color:#ffcc66}

.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:12px;
    font-size:12px;
    background:#333;
    margin-top:5px;
}

.actions a{
    color:#ff9900;
    text-decoration:none;
    font-weight:600;
}
.actions a:hover{text-decoration:underline}

@media(max-width:700px){
    .card{
        flex-direction:column;
        align-items:flex-start;
        gap:12px;
    }
}
</style>
</head>

<body>

<?php include(__DIR__ . "/../templates/navbar.php"); ?>

<div class="main">

<h1>Manage Users</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert success">User deleted successfully</div>
<?php elseif (isset($_GET['err'])): ?>
    <div class="alert error">Admin account cannot be deleted</div>
<?php endif; ?>

<div class="tabs">
    <button class="active" onclick="openTab('students', this)">Students</button>
    <button onclick="openTab('organizers', this)">Organizers</button>
</div>

<!-- STUDENTS -->
<div id="students" class="tab">
    <div class="list">
        <?php while ($u = mysqli_fetch_assoc($students)): ?>
        <div class="card">
            <div class="info">
                <b><?= htmlspecialchars($u['name']) ?></b><br>
                <?= htmlspecialchars($u['email']) ?><br>
                <span class="badge">Student</span>
            </div>
            <div class="actions">
                <a href="edit_user.php?id=<?= $u['user_id'] ?>">Edit</a> |
                <a href="?delete=<?= $u['user_id'] ?>"
                   onclick="return confirm('Delete this user?')">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- ORGANIZERS -->
<div id="organizers" class="tab" style="display:none">
    <div class="list">
        <?php while ($u = mysqli_fetch_assoc($organizers)): ?>
        <div class="card">
            <div class="info">
                <b><?= htmlspecialchars($u['name']) ?></b><br>
                <?= htmlspecialchars($u['email']) ?><br>
                <span class="badge">Organizer</span>
            </div>
            <div class="actions">
                <a href="edit_user.php?id=<?= $u['user_id'] ?>">Edit</a> |
                <a href="?delete=<?= $u['user_id'] ?>"
                   onclick="return confirm('Delete this user?')">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</div>

<script>
function openTab(id, btn){
    document.querySelectorAll(".tab").forEach(t => t.style.display = "none");
    document.querySelectorAll(".tabs button").forEach(b => b.classList.remove("active"));
    document.getElementById(id).style.display = "block";
    btn.classList.add("active");
}
</script>

</body>
</html>
