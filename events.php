<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("config/db.php");

/* ================= AJAX PART ================= */
if (
    isset($_POST['search']) ||
    isset($_POST['category']) ||
    isset($_POST['sort'])
) {

    $search   = isset($_POST['search']) ? $_POST['search'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $sort     = isset($_POST['sort']) ? $_POST['sort'] : 'new';

    $sql = "SELECT * FROM events WHERE status='approved'";

    if ($search != '') {
        $sql .= " AND title LIKE '%$search%'";
    }
    if ($category != '') {
        $sql .= " AND category='$category'";
    }

    $sql .= ($sort == 'old') 
        ? " ORDER BY event_date ASC" 
        : " ORDER BY event_date DESC";

    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) == 0) {
        echo "<p class='no-events'>No events found</p>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {

        $img = !empty($row['event_image'])
            ? "/EventHub_Sem6/public/" . $row['event_image']
            : "/EventHub_Sem6/public/images/default_event.png";
        ?>

        <div class="card">

            <img src="<?= $img ?>" class="event-img">

            <div class="card-body">
                <h3><?= htmlspecialchars($row['title']) ?></h3>

                <div class="meta">
                    📅 <?= date("d M Y", strtotime($row['event_date'])) ?><br>
                    📍 <?= htmlspecialchars($row['venue']) ?>
                </div>

                <p>
                    <?= substr(strip_tags($row['description']), 0, 90) ?>...
                </p>
            </div>

            <div class="card-actions">

                <a href="event_details.php?id=<?= $row['event_id'] ?>" class="btn view">
                    View
                </a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') { ?>
                    <a href="student/register_event.php?id=<?= $row['event_id'] ?>" class="btn register">
                        Register
                    </a>
                <?php } else { ?>
                    <button class="btn register" onclick="askLogin()">
                        Register
                    </button>
                <?php } ?>

            </div>

        </div>
        <?php
    }
    exit;
}

/* ================= PAGE LOAD ================= */
$catRes = mysqli_query(
    $conn,
    "SELECT DISTINCT category FROM events WHERE status='approved'"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events | EventHub</title>

    <style>
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)),
                url("public/uploads/images/bg1.jpg") center/cover no-repeat fixed;
            color:#fff;
            font-family:'Segoe UI',sans-serif;
        }

        .container{
            max-width:1200px;
            margin:130px auto 50px;
            padding:0 20px;
        }

        h1{text-align:center;margin-bottom:30px;}

        /* FILTERS */
        .filters{
            display:flex;
            gap:15px;
            margin-bottom:30px;
        }
        .filters input,.filters select{
            padding:10px 16px;
            border-radius:25px;
            border:none;
        }
        .filters input{flex:1;}

        /* GRID FIX */
        .events{
            display:grid;
            grid-template-columns:repeat(3, 1fr);
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
            height:100%;
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
            font-size:18px;
        }

        .meta{
            font-size:13px;
            color:#aaa;
            margin-bottom:8px;
        }

        .card-body p{
            font-size:14px;
            color:#ddd;
        }

        .card-actions{
            display:flex;
            gap:10px;
            padding:15px;
        }

        .btn{
            flex:1;
            text-align:center;
            padding:10px;
            border-radius:20px;
            text-decoration:none;
            font-weight:600;
            cursor:pointer;
            border:none;
        }

        .btn.view{
            background:#444;
            color:#fff;
        }

        .btn.register{
            background:linear-gradient(135deg,#ff7a18,#ffb347);
            color:#000;
        }

        .no-events{
            text-align:center;
            color:#ccc;
        }
    </style>
</head>
<body>

<?php include("templates/navbar.php"); ?>

<div class="container">
    <h1>Explore Events</h1>

    <div class="filters">
        <input type="text" id="search" placeholder="Search events">
        <select id="category">
            <option value="">All Categories</option>
            <?php while($c=mysqli_fetch_assoc($catRes)){ ?>
                <option value="<?= $c['category'] ?>"><?= $c['category'] ?></option>
            <?php } ?>
        </select>
        <select id="sort">
            <option value="new">Newest</option>
            <option value="old">Oldest</option>
        </select>
    </div>

    <div class="events" id="eventData"></div>
</div>

<script>
function loadEvents(){
    const s = search.value;
    const c = category.value;
    const so = sort.value;

    fetch("events.php",{
        method:"POST",
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`search=${s}&category=${c}&sort=${so}`
    })
    .then(r=>r.text())
    .then(d=>eventData.innerHTML=d);
}

search.onkeyup = loadEvents;
category.onchange = loadEvents;
sort.onchange = loadEvents;
loadEvents();

function askLogin(){
    if(confirm("You need to login to register for an event. Login now?")){
        window.location.href = "auth/login.php";
    }
}
</script>

</body>
</html>

