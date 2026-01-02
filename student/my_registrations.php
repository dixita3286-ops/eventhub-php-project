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
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)),
                url("../public/images/bg1.jpg") center/cover no-repeat fixed;
            color:#fff;
            font-family:'Segoe UI',sans-serif;
        }

        .container{
            max-width:1200px;
            margin:130px auto 50px;
            padding:0 20px;
        }

        h1{
            text-align:center;
            margin-bottom:30px;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:30px;
        }

        @media(max-width:900px){
            .grid{grid-template-columns:repeat(2,1fr);}
        }
        @media(max-width:600px){
            .grid{grid-template-columns:1fr;}
        }

        .card{
            background:rgba(255,255,255,0.08);
            border-radius:18px;
            display:flex;
            flex-direction:column;
            backdrop-filter:blur(10px);
        }

        .event-img{
            height:180px;
            width:100%;
            object-fit:cover;
            border-radius:18px 18px 0 0;
        }

        .card-body{
            padding:15px;
            flex:1;
        }

        .card-body h3{
            color:#ffb347;
            margin-bottom:8px;
        }

        .meta{
            font-size:13px;
            color:#aaa;
            margin-bottom:8px;
        }

        .meta span{
            display:block;
        }

        .status{
            margin-top:8px;
            font-size:14px;
        }

        .paid{color:#4caf50;}
        .pending{color:#ff9800;}

        .card-actions{
            padding:15px;
            display:flex;
            gap:10px;
        }

        .btn{
            flex:1;
            text-align:center;
            padding:10px;
            border-radius:20px;
            text-decoration:none;
            font-weight:600;
        }

        .view{
            background:#444;
            color:#fff;
        }

        .no-data{
            text-align:center;
            color:#ccc;
            margin-top:80px;
        }
    </style>
</head>
<body>

<?php include("../templates/navbar.php"); ?>

<div class="container">
    <h1>My Registered Events</h1>

    <?php if (mysqli_num_rows($res) == 0) { ?>

        <p class="no-data">You have not registered for any events yet.</p>

    <?php } else { ?>

    <div class="grid">

        <?php while ($row = mysqli_fetch_assoc($res)) {

            $img = !empty($row['event_image'])
                ? "/EventHub_Sem6/public/" . $row['event_image']
                : "/EventHub_Sem6/public/images/default_event.png";
        ?>

        <div class="card">

            <img src="<?php echo $img; ?>" class="event-img">

            <div class="card-body">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                <div class="meta">
                    <span>📅 <?php echo date("d M Y", strtotime($row['event_date'])); ?></span>
                    <span>📍 <?php echo htmlspecialchars($row['venue']); ?></span>
                </div>

                <div class="status">
                    💳 ₹<?php echo number_format($row['payment_amount'],2); ?>
                    (<?php echo htmlspecialchars($row['payment_status']); ?>)
                </div>
            </div>

            <div class="card-actions">
                <a href="/EventHub_Sem6/event_details.php?id=<?php echo $row['event_id']; ?>" class="btn view">
                    View Details
                </a>
            </div>

        </div>

        <?php } ?>

    </div>

    <?php } ?>

</div>

</body>
</html>
