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

    /* BASIC SAFE QUERY */
    $sql = "SELECT * FROM events WHERE status='approved'";

    if ($search != '') {
        $sql .= " AND title LIKE '%$search%'";
    }

    if ($category != '') {
        $sql .= " AND category='$category'";
    }

    /* SORTING (ONLY USING event_date) */
    if ($sort == 'old') {
        $sql .= " ORDER BY event_date ASC";
    } elseif ($sort == 'date') {
        $sql .= " ORDER BY event_date ASC";
    } else {
        $sql .= " ORDER BY event_date DESC";
    }

    $res = mysqli_query($conn, $sql);

    if (!$res) {
        die("SQL ERROR: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($res) == 0) {
        echo "<p style='color:#ccc'>No events found</p>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>

            <div class="meta">
                📅 <?php echo date("d M Y", strtotime($row['event_date'])); ?>
                &nbsp;|&nbsp;
                📍 <?php echo htmlspecialchars($row['venue']); ?>
            </div>

            <p>
                <?php echo substr(strip_tags($row['description']), 0, 120); ?>...
            </p>

            <a href="event_details.php?id=<?php echo $row['event_id']; ?>">
                View Details
            </a>
        </div>
        <?php
    }
    exit;
}

/* ================= PAGE LOAD PART ================= */

/* FETCH CATEGORIES */
$catSql = "SELECT DISTINCT category FROM events WHERE status='approved'";
$catRes = mysqli_query($conn, $catSql);
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

        /* FILTER BAR */
        .filters{
            display:flex;
            gap:15px;
            flex-wrap:wrap;
            margin-bottom:35px;
        }

        .filters input,
        .filters select{
            padding:10px 16px;
            border-radius:25px;
            border:none;
            outline:none;
            font-size:14px;
        }

        .filters input{
            flex:1;
        }

        /* EVENTS GRID */
        .events{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:25px;
        }

        .card{
            background:rgba(255,255,255,0.08);
            border-radius:16px;
            padding:20px;
            backdrop-filter:blur(10px);
        }

        .card h3{
            margin-bottom:10px;
            color:#ffb347;
        }

        .meta{
            font-size:13px;
            color:#aaa;
            margin-bottom:12px;
        }

        .card p{
            font-size:14px;
            color:#ddd;
            margin-bottom:15px;
        }

        .card a{
            display:inline-block;
            padding:8px 18px;
            background:linear-gradient(135deg,#ff7a18,#ffb347);
            color:#000;
            border-radius:20px;
            text-decoration:none;
            font-weight:600;
        }
    </style>
</head>
<body>

<?php include("templates/navbar.php"); ?>

<div class="container">

    <h1>Explore Events</h1>

    <!-- FILTERS -->
    <div class="filters">
        <input type="text" id="search" placeholder="Search events...">

        <select id="category">
            <option value="">All Categories</option>
            <?php
            if ($catRes) {
                while ($c = mysqli_fetch_assoc($catRes)) {
                    echo "<option value='".$c['category']."'>".$c['category']."</option>";
                }
            }
            ?>
        </select>

        <select id="sort">
            <option value="new">Newest First</option>
            <option value="old">Oldest First</option>
            <option value="date">Event Date</option>
        </select>
    </div>

    <!-- EVENTS -->
    <div class="events" id="eventData"></div>

</div>

<script>
function loadEvents(){
    var search   = document.getElementById("search").value;
    var category = document.getElementById("category").value;
    var sort     = document.getElementById("sort").value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST","events.php",true);
    xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            document.getElementById("eventData").innerHTML = xhr.responseText;
        }
    };

    xhr.send(
        "search="+encodeURIComponent(search)+
        "&category="+encodeURIComponent(category)+
        "&sort="+encodeURIComponent(sort)
    );
}

document.getElementById("search").onkeyup = loadEvents;
document.getElementById("category").onchange = loadEvents;
document.getElementById("sort").onchange = loadEvents;

/* INITIAL LOAD */
loadEvents();
</script>

</body>
</html>
