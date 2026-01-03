<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

$conn = mysqli_connect("localhost", "root", "", "eventhub_db");
if (!$conn) die("Database connection failed");

$search   = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$sort     = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'desc';

/* AJAX */
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    $sql = "SELECT * FROM events WHERE status='approved'";

    if ($search)
        $sql .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR category LIKE '%$search%')";

    if ($category)
        $sql .= " AND category='$category'";

    $sql .= ($sort === "asc") ? " ORDER BY date ASC" : " ORDER BY date DESC";

    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) == 0) {
        echo "<p style='text-align:center;color:#aaa;'>No events found.</p>";
        exit;
    }

    while ($r = mysqli_fetch_assoc($res)) {
        $img = !empty($r['event_image']) ? "uploads/".$r['event_image'] : "uploads/images/default.jpg";

        echo "
        <div class='event-card'>
            <img src='$img' loading='lazy' alt='Event'>
            <div class='event-info'>
                <h3>{$r['title']}</h3>
                <p><strong>Category:</strong> {$r['category']}</p>
                <p><strong>Date:</strong> {$r['date']}</p>
            </div>
            <div class='event-actions'>
                <a href='event_details.php?id={$r['event_id']}'>View Details</a>
                <span>|</span>
                <a href='login.php?msg=Please login to register'>Register</a>
            </div>
        </div>";
    }
    exit;
}

$sql = "SELECT * FROM events WHERE status='approved' ORDER BY date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>EventHub - Events</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:Poppins,sans-serif;
    background:#0d0d0d;
    color:#fff;
}

/* NAVBAR */
.navbar{
    position:fixed;
    top:0;
    width:100%;
    background:#000;
    padding:12px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #ff9900;
    z-index:2000;
}
.navbar img{height:40px;border-radius:6px}

/* MAIN */
.main{
    padding:120px 90px 90px;
    contain:layout paint;
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
    flex-wrap:wrap;
    margin-bottom:25px;
}
.filters input,
.filters select{
    padding:10px 14px;
    border-radius:10px;
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

/* CARD (GPU OPTIMIZED) */
.event-card{
    background:rgba(255,255,255,0.08);
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,.45);
    transition:transform .25s ease;
    will-change:transform;
    transform:translateZ(0);
}

.event-card:hover{
    transform:translateY(-5px);
}

/* IMAGE */
.event-card img{
    width:100%;
    height:240px;
    object-fit:cover;
}

/* INFO */
.event-info{padding:16px 18px}
.event-info h3{color:#ff9900;margin-bottom:6px}
.event-info p{color:#ddd;font-size:14px}

/* ACTIONS */
.event-actions{
    padding:12px 18px;
    border-top:1px solid rgba(255,255,255,0.05);
}
.event-actions a{
    color:#ff8c00;
    font-weight:600;
    text-decoration:none;
}

/* DISABLE HOVER DURING SCROLL */
body.scrolling .event-card:hover{
    transform:none;
}
</style>
</head>

<body>
<?php include(__DIR__ . "/templates/navbar.php"); ?>


    <div class="event-grid" id="eventGrid">
        <?php while($row=mysqli_fetch_assoc($result)): 
            $img = !empty($row['event_image']) ? "public/uploads/".$row['event_image'] : "images/default.jpg";
        ?>
        <div class="event-card">
            <img src="<?= $img ?>" loading="lazy">
            <div class="event-info">
                <h3><?= $row['title'] ?></h3>
                <p><b>Category:</b> <?= $row['category'] ?></p>
                <p><b>Date:</b> <?= $row['date'] ?></p>
            </div>
            <div class="event-actions">
                <a href="event_details.php?id=<?= $row['event_id'] ?>">View Details</a>
                |
                <a href="login.php?msg=Please login to register">Register</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
/* FILTER AJAX */
let timer;
function loadEvents(){
    const q=document.getElementById("searchInput").value;
    const c=document.getElementById("category").value;
    const s=document.getElementById("sortDate").value;

    fetch(`events.php?ajax=1&search=${q}&category=${c}&sort=${s}`)
        .then(r=>r.text())
        .then(d=>document.getElementById("eventGrid").innerHTML=d);
}

["keyup","change"].forEach(e=>{
    searchInput.addEventListener(e,()=>{clearTimeout(timer);timer=setTimeout(loadEvents,300)})
    category.addEventListener(e,loadEvents)
    sortDate.addEventListener(e,loadEvents)
});

/* SCROLL OPTIMIZATION */
let scrollTimer;
window.addEventListener("scroll",()=>{
    document.body.classList.add("scrolling");
    clearTimeout(scrollTimer);
    scrollTimer=setTimeout(()=>document.body.classList.remove("scrolling"),150);
});
</script>

</body>
</html>
