<?php
session_start();
include("../config/db.php");

/* SECURITY: ONLY ADMIN */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* HANDLE APPROVE / REJECT */
if (isset($_GET['action']) && isset($_GET['id'])) {

    $event_id = (int)$_GET['id'];
    $action   = $_GET['action'];

    if ($action === 'approve') {
        $status = 'approved';
    } elseif ($action === 'reject') {
        $status = 'rejected';
    } else {
        $status = '';
    }

    if ($status != '') {
        mysqli_query(
            $conn,
            "UPDATE events SET status='$status' WHERE event_id=$event_id"
        );
    }

    header("Location: manage_events.php");
    exit();
}

/* FETCH PENDING EVENTS */
$res = mysqli_query(
    $conn,
    "SELECT e.*, u.name AS organizer_name
     FROM events e
     JOIN users u ON e.created_by = u.user_id
     WHERE e.status='pending'
     ORDER BY e.event_date ASC"
);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Events | Admin</title>

    <style>
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)),
                url("../public/images/bg1.jpg") center/cover no-repeat fixed;
            font-family:'Segoe UI',sans-serif;
            color:#fff;
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
            margin-bottom:6px;
        }

        .meta{
            font-size:13px;
            color:#ccc;
            margin-bottom:10px;
        }

        .card-body p{
            font-size:14px;
            color:#ddd;
        }

        .actions{
            display:flex;
            gap:10px;
            padding:15px;
        }

        .btn{
            flex:1;
            padding:10px;
            border-radius:25px;
            border:none;
            font-weight:600;
            cursor:pointer;
            text-align:center;
            text-decoration:none;
        }

        .approve{
            background:#4caf50;
            color:#fff;
        }

        .reject{
            background:#f44336;
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
    <h1>Pending Event Approvals</h1>

    <?php if (mysqli_num_rows($res) == 0) { ?>

        <p class="no-data">No pending events.</p>

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
                    📅 <?php echo date("d M Y", strtotime($row['event_date'])); ?><br>
                    📍 <?php echo htmlspecialchars($row['venue']); ?><br>
                    👤 Organizer: <?php echo htmlspecialchars($row['organizer_name']); ?>
                </div>

                <p>
                    <?php echo substr(strip_tags($row['description']),0,100); ?>...
                </p>
            </div>

            <div class="actions">
                <a href="manage_events.php?action=approve&id=<?php echo $row['event_id']; ?>"
                   class="btn approve"
                   onclick="return confirm('Approve this event?')">
                    Approve
                </a>

                <a href="manage_events.php?action=reject&id=<?php echo $row['event_id']; ?>"
                   class="btn reject"
                   onclick="return confirm('Reject this event?')">
                    Reject
                </a>
            </div>

        </div>

        <?php } ?>

    </div>

    <?php } ?>

</div>

</body>
</html>
