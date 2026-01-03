<?php
session_start();
include("config/db.php");

/* FETCH APPROVED EVENTS */
$sql = "SELECT * FROM events WHERE status='approved' ORDER BY event_date DESC";
$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Events | EventHub</title>

    <style>
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,.75), rgba(0,0,0,.9)),
                url("public/images/bg1.jpg") center/cover no-repeat fixed;
            color:#fff;
            font-family:'Segoe UI',sans-serif;
            padding-top:80px;
            overflow-x:hidden;
        }

        .container{
            max-width:1200px;
            margin:40px auto 60px;
            padding:0 20px;
        }

        h1{
            text-align:center;
            margin-bottom:35px;
        }

        /* GRID */
        .events{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:30px;
        }

        @media(max-width:900px){
            .events{grid-template-columns:repeat(2,1fr);}
        }
        @media(max-width:600px){
            .events{grid-template-columns:1fr;}
        }

        /* CARD */
        .card{
            background:rgba(255,255,255,0.08);
            border-radius:18px;
            display:flex;
            flex-direction:column;
            backdrop-filter:blur(10px);
        }

        .event-img{
            width:100%;
            height:190px;
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
            color:#ccc;
            margin-bottom:10px;
        }

        .card-body p{
            font-size:14px;
            color:#ddd;
        }

        .card-actions{
            padding:15px;
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .btn{
            flex:1;
            padding:10px;
            border-radius:22px;
            text-decoration:none;
            text-align:center;
            font-weight:600;
            cursor:pointer;
        }

        .view{
            background:#444;
            color:#fff;
        }

        .action{
            background:linear-gradient(135deg,#ff7a18,#ffb347);
            color:#000;
        }
    </style>
</head>
<body>

<?php include("templates/navbar.php"); ?>

<div class="container">
    <h1>Explore Events</h1>

    <?php if(mysqli_num_rows($res) == 0){ ?>
        <p style="text-align:center;color:#ccc;">No events available.</p>
    <?php } else { ?>

    <div class="events">

        <?php while($row = mysqli_fetch_assoc($res)){

            $img = !empty($row['event_image'])
                ? "/EventHub_Sem6/public/".$row['event_image']
                : "/EventHub_Sem6/public/images/default_event.png";
        ?>

        <div class="card">

            <img src="<?php echo $img; ?>" class="event-img">

            <div class="card-body">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                <div class="meta">
                    📅 <?php echo date("d M Y", strtotime($row['event_date'])); ?><br>
                    📍 <?php echo htmlspecialchars($row['venue']); ?>
                </div>

                <p><?php echo substr(strip_tags($row['description']),0,90); ?>...</p>
            </div>

            <!-- CARD ACTIONS -->
            <div class="card-actions">

                <!-- VIEW DETAILS (ALL) -->
                <a href="event_details.php?id=<?php echo $row['event_id']; ?>" class="btn view">
                    View Details
                </a>

                <!-- GUEST -->
                <?php if(!isset($_SESSION['role'])){ ?>
                    <a href="auth/login.php" class="btn action">
                        Register
                    </a>
                <?php } ?>

                <!-- STUDENT -->
                <?php if(isset($_SESSION['role']) && $_SESSION['role']==='student'){ ?>
                    <a href="student/register_event.php?id=<?php echo $row['event_id']; ?>" class="btn action">
                        Register
                    </a>
                <?php } ?>

                <!-- ORGANIZER -->
                <?php if(isset($_SESSION['role']) && $_SESSION['role']==='organizer'){ ?>
                    <!-- Only View Details -->
                <?php } ?>

                <!-- ADMIN -->
                <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'){ ?>
                    <a href="admin/view_registrations.php?event_id=<?php echo $row['event_id']; ?>" class="btn action">
                        View Registrations
                    </a>
                    <a href="admin/modify_event.php?event_id=<?php echo $row['event_id']; ?>" class="btn action">
                        Modify
                    </a>
                <?php } ?>

            </div>

        </div>

        <?php } ?>

    </div>

    <?php } ?>
</div>

</body>
</html>
