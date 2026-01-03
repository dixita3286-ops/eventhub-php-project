<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("config/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event.");
}

$event_id = (int)$_GET['id'];

$sql = "SELECT * FROM events WHERE event_id = $event_id AND status = 'approved'";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    die("Event not found or not approved.");
}

$event = mysqli_fetch_assoc($res);

$imagePath = !empty($event['event_image'])
    ? "/EventHub_Sem6/public/" . $event['event_image']
    : "/EventHub_Sem6/public/images/default_event.png";

$filePath = !empty($event['event_file'])
    ? "/EventHub_Sem6/public/" . $event['event_file']
    : "";
?>
<!DOCTYPE html>
<html>
<head>
<title><?= htmlspecialchars($event['title']) ?> | EventHub</title>

<style>
*{box-sizing:border-box;margin:0;padding:0}

body{
    min-height:100vh;
    background:
        linear-gradient(to bottom, rgba(0,0,0,.8), rgba(0,0,0,.95)),
        url("public/uploads/images/bg1.jpg") center/cover no-repeat;
    color:#fff;
    font-family:'Segoe UI',sans-serif;
}

/* CONTAINER */
.container{
    max-width:900px;
    margin:130px auto 50px;
    padding:0 20px;
    contain:layout paint;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.08);
    border-radius:18px;
    padding:25px;
    backdrop-filter:blur(12px);
    will-change:transform;
    transform:translateZ(0);
}

/* IMAGE */
.event-img{
    width:100%;
    height:350px;
    object-fit:cover;
    border-radius:14px;
    margin-bottom:20px;
}

/* TITLE */
h1{
    margin-bottom:10px;
    color:#ffb347;
}

/* META */
.meta{
    font-size:14px;
    color:#ccc;
    margin-bottom:20px;
}
.meta span{
    display:inline-block;
    margin-right:20px;
}

/* DESCRIPTION */
.description{
    font-size:15px;
    line-height:1.6;
    margin-bottom:25px;
}

/* ACTIONS */
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

<?php include(__DIR__ . "/templates/navbar.php"); ?>


<div class="container">
    <div class="card">

        <!-- IMAGE (lazy loaded) -->
        <img src="<?= $imagePath ?>" loading="lazy" class="event-img" alt="Event Image">

        <h1><?= htmlspecialchars($event['title']) ?></h1>

        <div class="meta">
            <span><?= date("d M Y", strtotime($event['event_date'])) ?></span>
            <span><?= htmlspecialchars($event['venue']) ?></span>
            <span><?= htmlspecialchars($event['category']) ?></span>
            <span><?= number_format($event['registration_fee'], 2) ?></span>
        </div>

        <div class="description">
            <?= nl2br(htmlspecialchars($event['description'])) ?>
        </div>

        <div class="actions">
            <?php if ($filePath): ?>
                <a href="<?= $filePath ?>" class="btn-secondary" download>
                    Download Event File
                </a>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
                <a href="student/register_event.php?id=<?= $event_id ?>" class="btn-primary">
                    Register for Event
                </a>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
