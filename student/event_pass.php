<?php
session_start();
include("../config/db.php");

/* SECURITY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

/* VALIDATE REGISTRATION ID */
if (!isset($_GET['rid']) || !is_numeric($_GET['rid'])) {
    die("Invalid request");
}

$registration_id = (int)$_GET['rid'];
$student_id      = $_SESSION['user_id'];

/* FETCH REGISTRATION + EVENT */
$sql = "
SELECT 
    r.registration_id,
    r.payment_amount,
    r.payment_status,
    e.title,
    e.event_date,
    e.venue,
    e.category,
    u.name AS student_name
FROM registrations r
JOIN events e ON r.event_id = e.event_id
JOIN users u ON r.student_id = u.user_id
WHERE r.registration_id = $registration_id
AND r.student_id = $student_id
";

$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) == 0) {
    die("Pass not found");
}

$data = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html>
<head>
<title>Event Pass</title>

<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#f2f2f2;
}
.pass{
    max-width:600px;
    margin:50px auto;
    background:#fff;
    border-radius:12px;
    padding:25px;
    border:2px dashed #333;
}
.header{
    text-align:center;
    margin-bottom:20px;
}
.header h2{
    margin:0;
}
.details{
    margin-top:20px;
}
.details p{
    font-size:15px;
    margin:8px 0;
}
.qr{
    width:120px;
    height:120px;
    background:#ddd;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:20px auto;
    font-size:12px;
}
.print{
    text-align:center;
    margin-top:20px;
}
button{
    padding:10px 25px;
    border:none;
    border-radius:20px;
    background:#ff7a18;
    font-weight:600;
    cursor:pointer;
}
@media print{
    button{display:none;}
    body{background:#fff;}
}
</style>
</head>
<body>

<div class="pass">

    <div class="header">
        <h2>🎟 Event Pass</h2>
        <p><strong>EventHub</strong></p>
    </div>

    <div class="details">
        <p><strong>Student Name:</strong> <?php echo htmlspecialchars($data['student_name']); ?></p>
        <p><strong>Event:</strong> <?php echo htmlspecialchars($data['title']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($data['category']); ?></p>
        <p><strong>Date:</strong> <?php echo date("d M Y", strtotime($data['event_date'])); ?></p>
        <p><strong>Venue:</strong> <?php echo htmlspecialchars($data['venue']); ?></p>
        <p><strong>Registration ID:</strong> #<?php echo $data['registration_id']; ?></p>
        <p><strong>Payment:</strong> ₹<?php echo number_format($data['payment_amount'],2); ?> (<?php echo $data['payment_status']; ?>)</p>
    </div>

    <div class="qr">
        QR CODE
    </div>

    <div class="print">
        <button onclick="window.print()">Download / Print Pass</button>
    </div>

</div>

</body>
</html>
