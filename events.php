<?php
session_start();

/* SESSION ROLE (PHP 5 SAFE) */
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

/* DATABASE CONNECTION (CENTRAL FILE) */
include(__DIR__ . "/config/db.php");

/* GET VALUES (PHP 5 SAFE) */
$search   = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort     = isset($_GET['sort']) ? $_GET['sort'] : 'desc';

/* ================= AJAX FILTER ================= */
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    $search   = mysqli_real_escape_string($conn, $search);
    $category = mysqli_real_escape_string($conn, $category);
    $sort     = mysqli_real_escape_string($conn, $sort);

    $sql = "SELECT * FROM events WHERE status='approved'";

    if ($search != '') {
        $sql .= " AND (title LIKE '%$search%' 
                  OR category LIKE '%$search%' 
                  OR venue LIKE '%$search%')";
    }

    if ($category != '') {
        $sql .= " AND category='$category'";
    }

    if ($sort == 'asc') {
        $sql .= " ORDER BY event_date ASC";
    } else {
        $sql .= " ORDER BY event_date DESC";
    }

    $res = mysqli_query($conn, $sql);

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<p style='text-align:center;color:#aaa;'>No events found</p>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {

        $img = !empty($row['event_image'])
            ? "public/uploads/".$row['event_image']
            : "images/default.jpg";

        echo "
        <div class='event-card'>
            <img src='$img'>
            <div class='event-info'>
                <h3>{$row['title']}</h3>
                <p><b>Category:</b> {$row['category']}</p>
                <p><b>Date:</b> {$row['event_date']}</p>
            </div>
            <div class='event-actions'>
                <a href='event_details.php?id={$row['event_id']}'>View Details</a> |
                <a href='login.php'>Register</a>
            </div>
        </div>";
    }
    exit;
}
/* ================================================= */

/* NORMAL PAGE LOAD */
$sql = "SELECT * FROM events WHERE status='approved' ORDER BY event_date DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>EventHub | Events</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}

body{
    font-family:Poppins,sans-serif;
    background:#0d0d0d;
    color:#fff;
}

.main{
    padding:120px 90px;
}

h1{
    text-align:center;
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffcc66;
    margin-bottom:25px;
}

/* FILTERS */
.filters{
    display:flex;
    justify-content:center;
    gap:15px;
    margin-bottom:30px;
}

.filters input,
.filters select{
    padding:10px;
    border-radius:8px;
    background:#000;
    color:#fff;
    border:none;
}

/* GRID */
.event-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
    gap:25px;
}

/* CARD */
.event-card{
    background:rgba(255,255,255,0.08);
    border-radius:18px;
    overflow:hidden;
}

.event-card img{
    width:100%;
    height:220px;
    object-fit:cover;
}

.event-info{
    padding:15px;
}

.event-info h3{
    color:#ff9900;
}

.event-actions{
    padding:12px 15px;
    border-top:1px solid rgba(255,255,255,0.1);
}

.event-actions a{
    color:#ff8c00;
    font-weight:600;
    text-decoration:none;
}
</style>
</head>

<body>

<?php include(__DIR__ . "/templates/navbar.php"); ?>

<div class="main">

<h1>Upcoming Events</h1>

<div class="filters">
    <input type="text" id="searchInput" placeholder="Search events">
    <select id="category">
        <option value="">All Categories</option>
        <option value="Workshop">Workshop</option>
        <option value="Seminar">Seminar</option>
        <option value="Cultural">Cultural</option>
        <option value="Sports">Sports</option>
    </select>
    <select id="sortDate">
        <option value="desc">Latest</option>
        <option value="asc">Oldest</option>
    </select>
</div>

<div class="event-grid" id="eventGrid">
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<?php
$img = !empty($row['event_image'])
    ? "public/".$row['event_image']
    : "images/default.jpg";
?>
    <div class="event-card">
        <img src="<?= $img ?>">
        <div class="event-info">
            <h3><?= $row['title'] ?></h3>
            <p><b>Category:</b> <?= $row['category'] ?></p>
            <p><b>Date:</b> <?= $row['event_date'] ?></p>
        </div>
        <div class="event-actions">
            <a href="event_details.php?id=<?= $row['event_id'] ?>">View Details</a> |
            <a href="login.php">Register</a>
        </div>
    </div>
<?php endwhile; ?>
</div>

</div>

<script>
function loadEvents(){
    var q = document.getElementById("searchInput").value;
    var c = document.getElementById("category").value;
    var s = document.getElementById("sortDate").value;

    var xhr = new XMLHttpRequest();
    xhr.open("GET","events.php?ajax=1&search="+q+"&category="+c+"&sort="+s,true);
    xhr.onload = function(){
        document.getElementById("eventGrid").innerHTML = this.responseText;
    };
    xhr.send();
}

document.getElementById("searchInput").onkeyup = loadEvents;
document.getElementById("category").onchange = loadEvents;
document.getElementById("sortDate").onchange = loadEvents;
</script>

</body>
</html>
