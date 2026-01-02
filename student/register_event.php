<?php
session_start();
include("../config/db.php");

/* SECURITY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

/* VALIDATE EVENT ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event");
}

$event_id   = (int)$_GET['id'];
$student_id = $_SESSION['user_id'];

/* CHECK EVENT */
$eventRes = mysqli_query(
    $conn,
    "SELECT * FROM events WHERE event_id=$event_id AND status='approved'"
);

if (mysqli_num_rows($eventRes) == 0) {
    die("Event not found or not approved.");
}

$event = mysqli_fetch_assoc($eventRes);

/* CHECK ALREADY REGISTERED */
$check = mysqli_query(
    $conn,
    "SELECT * FROM registrations 
     WHERE event_id=$event_id AND student_id=$student_id"
);

if (mysqli_num_rows($check) > 0) {
    die("You are already registered for this event.");
}

/* HANDLE REGISTER */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $amount = $event['registration_fee'];

    mysqli_query(
        $conn,
        "INSERT INTO registrations 
        (event_id, student_id, payment_amount, payment_status)
        VALUES
        ($event_id, $student_id, $amount, 'paid')"
    );

    header("Location: register_success.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Event | EventHub</title>

    <style>
        body{
            background:#0d1117;
            color:#fff;
            font-family:'Segoe UI',sans-serif;
        }
        .box{
            max-width:420px;
            margin:130px auto;
            background:#1c1c1c;
            padding:25px;
            border-radius:14px;
            text-align:center;
        }
        h2{color:#ffb347;}
        .fee{
            font-size:22px;
            margin:15px 0;
        }
        button{
            padding:12px 30px;
            border:none;
            border-radius:25px;
            background:linear-gradient(135deg,#ff7a18,#ffb347);
            font-weight:600;
            cursor:pointer;
        }
    </style>
</head>
<body>

<div class="box">
    <h2><?php echo htmlspecialchars($event['title']); ?></h2>

    <p>Registration Fee</p>
    <div class="fee">₹<?php echo number_format($event['registration_fee'],2); ?></div>

    <form method="POST">
        <button type="submit">Pay & Register</button>
    </form>
</div>

</body>
</html>
