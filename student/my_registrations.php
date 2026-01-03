<?php
session_start();
include("../config/db.php");

/* SECURITY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

/* FETCH REGISTRATIONS */
$sql = "
SELECT 
    r.registration_id,
    r.payment_amount,
    r.payment_status,
    e.event_id,
    e.title,
    e.event_date,
    e.venue,
    e.event_image
FROM registrations r
JOIN events e ON r.event_id = e.event_id
WHERE r.student_id = $student_id
ORDER BY e.event_date DESC
";

$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Registrations | EventHub</title>

<style>
*{box-sizing:border-box;margin:0;padding:0}

body{
    background:#0d0d0d;
    color:#fff;
    font-family:'Segoe UI',sans-serif;
}

/* PAGE WRAPPER */
.page{
    max-width:1300px;
    margin:110px auto 60px;
    padding:0 25px;
}

/* HEADER */
.header{
    margin-bottom:35px;
}
.header h1{
    font-size:32px;
    font-weight:600;
}
.header p{
    color:#aaa;
    margin-top:6px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
    gap:25px;
}

/* CARD */
.card{
    background:#141414;
    border-radius:14px;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    box-shadow:0 10px 30px rgba(0,0,0,.4);
    transition:transform .25s ease;
    will-change:transform;
}

.card:hover{
    transform:translateY(-4px);
}

/* IMAGE */
.card img{
    width:100%;
    height:180px;
    object-fit:cover;
}

/* BODY */
.card-body{
    padding:16px;
    flex:1;
}

.card-body h3{
    font-size:18px;
    margin-bottom:8px;
    color:#ffb347;
}

.meta{
    font-size:13px;
    color:#bbb;
    margin-bottom:12px;
}
.meta span{
    display:block;
    margin-bottom:4px;
}

/* PAYMENT */
.payment{
    margin-top:auto;
    font-size:14px;
}
.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:12px;
    font-size:12px;
    font-weight:600;
    margin-left:6px;
}
.paid{background:#1e7e34;color:#fff}
.pending{background:#ff9800;color:#000}

/* ACTIONS */
.actions{
    display:flex;
    gap:10px;
    padding:15px;
    border-top:1px solid rgba(255,255,255,0.06);
}

.actions a{
    flex:1;
    text-align:center;
    padding:10px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    font-size:14px;
}

.view{
    background:#2a2a2a;
    color:#fff;
}

.pass{
    background:#ffb347;
    color:#000;
}

/* EMPTY STATE */
.empty{
    text-align:center;
    margin-top:100px;
    color:#aaa;
}
</style>
</head>

<body>

<?php include(__DIR__ . "/../templates/navbar.php"); ?>

<div class="page">

    <div class="header">
        <h1>My Registrations</h1>
        <p>All events you have successfully registered for</p>
    </div>

<?php if (mysqli_num_rows($res) == 0) { ?>

    <div class="empty">
        <p>You have not registered for any events yet.</p>
    </div>

<?php } else { ?>

    <div class="grid">

    <?php while ($row = mysqli_fetch_assoc($res)) {

        $img = !empty($row['event_image'])
            ? "/EventHub_Sem6/public/" . $row['event_image']
            : "/EventHub_Sem6/public/images/default_event.png";
    ?>

        <div class="card">

            <img src="<?= $img ?>" loading="lazy" alt="Event">

            <div class="card-body">
                <h3><?= htmlspecialchars($row['title']) ?></h3>

                <div class="meta">
                    <span>📅 <?= date("d M Y", strtotime($row['event_date'])) ?></span>
                    <span>📍 <?= htmlspecialchars($row['venue']) ?></span>
                </div>

                <div class="payment">
                    ₹<?= number_format($row['payment_amount'],2) ?>
                    <span class="badge <?= $row['payment_status']=='paid'?'paid':'pending' ?>">
                        <?= ucfirst($row['payment_status']) ?>
                    </span>
                </div>
            </div>

            <div class="actions">
                <a href="/EventHub_Sem6/event_details.php?id=<?= $row['event_id'] ?>" class="view">
                    View Details
                </a>
                <a href="event_pass.php?rid=<?= $row['registration_id'] ?>" class="pass">
                    Download Pass
                </a>
            </div>

        </div>

    <?php } ?>

    </div>

<?php } ?>

</div>

</body>
</html>
