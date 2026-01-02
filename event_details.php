<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("config/db.php");

/* VALIDATE EVENT ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event.");
}

$event_id = (int)$_GET['id'];

/* FETCH EVENT */
$sql = "SELECT * FROM events WHERE event_id = $event_id AND status = 'approved'";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    die("Event not found or not approved.");
}

$event = mysqli_fetch_assoc($res);

/* IMAGE PATH */
if (!empty($event['event_image'])) {
    $imagePath = "/EventHub_Sem6/public/" . $event['event_image'];
} else {
    $imagePath = "/EventHub_Sem6/public/images/default_event.png";
}

/* FILE PATH (PDF / DOC) */
$filePath = "";
if (!empty($event['event_file'])) {
    $filePath = "/EventHub_Sem6/public/" . $event['event_file'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($event['title']); ?> | EventHub</title>

    <style>
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,.8), rgba(0,0,0,.95)),
                url("public/uploads/images/bg1.jpg") center/cover no-repeat fixed;
            color:#fff;
            font-family:'Segoe UI',sans-serif;
        }

        .container{
            max-width:900px;
            margin:130px auto 50px;
            padding:0 20px;
        }

        .card{
            background:rgba(255,255,255,0.08);
            border-radius:18px;
            padding:25px;
            backdrop-filter:blur(12px);
        }

        .event-img{
            width:100%;
            height:350px;
            object-fit:cover;
            border-radius:14px;
            margin-bottom:20px;
        }

        h1{
            margin-bottom:10px;
            color:#ffb347;
        }

        .meta{
            font-size:14px;
            color:#ccc;
            margin-bottom:20px;
        }

        .meta span{
            display:inline-block;
            margin-right:20px;
        }

        .description{
            font-size:15px;
            line-height:1.6;
            margin-bottom:25px;
        }

        .actions a{
            display:inline-block;
            margin-right:12px;
            padding:10px 22px;
            border-radius:25px;
            text-decoration:none;
            font-weight:600;
        }

        .btn-primary{
            background:linear-gradient(135deg,#ff7a18,#ffb347);
            color:#000;
        }

        .btn-secondary{
            border:1px solid #666;
            color:#fff;
        }
    </style>
</head>
<body>

<?php include("templates/navbar.php"); ?>

<div class="container">

    <div class="card">

        <!-- EVENT IMAGE -->
        <img src="<?php echo $imagePath; ?>" class="event-img" alt="Event Image">

        <!-- TITLE -->
        <h1><?php echo htmlspecialchars($event['title']); ?></h1>

        <!-- META INFO -->
        <div class="meta">
            <span><?php echo date("d M Y", strtotime($event['event_date'])); ?></span>
            <span><?php echo htmlspecialchars($event['venue']); ?></span>
            <span><?php echo htmlspecialchars($event['category']); ?></span>
            <span><?php echo number_format($event['registration_fee'], 2); ?></span>
        </div>

        <!-- DESCRIPTION -->
        <div class="description">
            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
        </div>

        <!-- ACTIONS -->
        <div class="actions">
            <?php if ($filePath != "") { ?>
                <a href="<?php echo $filePath; ?>" class="btn-secondary" download>
                    Download Event File
                </a>
            <?php } ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') { ?>
                <a href="student/register_event.php?id=<?php echo $event_id; ?>" class="btn-primary">
                    Register for Event
                </a>
            <?php } ?>
        </div>

    </div>

</div>

</body>
</html>
