<?php
session_start();
include("../config/db.php");

/* SECURITY: ADMIN ONLY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

/* VALIDATE EVENT ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event");
}

$event_id = (int)$_GET['id'];
$msg = "";

/* FETCH EVENT */
$res = mysqli_query($conn, "SELECT * FROM events WHERE event_id=$event_id");
if (mysqli_num_rows($res) == 0) {
    die("Event not found");
}
$event = mysqli_fetch_assoc($res);

/* UPDATE EVENT */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $event_date  = $_POST['event_date'];
    $venue       = mysqli_real_escape_string($conn, $_POST['venue']);
    $fee         = $_POST['registration_fee'];
    $status      = $_POST['status'];

    /* IMAGE UPLOAD */
    $event_image = $event['event_image'];
    if (!empty($_FILES['event_image']['name'])) {
        $imgFolder = "../public/uploads/images/";
        if (!is_dir($imgFolder)) mkdir($imgFolder,0777,true);

        $imgName = time() . "_" . basename($_FILES['event_image']['name']);
        move_uploaded_file($_FILES['event_image']['tmp_name'], $imgFolder.$imgName);
        $event_image = "uploads/images/".$imgName;
    }

    /* FILE UPLOAD */
    $event_file = $event['event_file'];
    if (!empty($_FILES['event_file']['name'])) {
        $fileFolder = "../public/uploads/files/";
        if (!is_dir($fileFolder)) mkdir($fileFolder,0777,true);

        $fileName = time() . "_" . basename($_FILES['event_file']['name']);
        move_uploaded_file($_FILES['event_file']['tmp_name'], $fileFolder.$fileName);
        $event_file = "uploads/files/".$fileName;
    }

    $update = "
        UPDATE events SET
        title='$title',
        description='$description',
        category='$category',
        event_date='$event_date',
        venue='$venue',
        registration_fee='$fee',
        event_image='$event_image',
        event_file='$event_file',
        status='$status'
        WHERE event_id=$event_id
    ";

    if (mysqli_query($conn, $update)) {
        $msg = "Event updated successfully.";
        $res = mysqli_query($conn, "SELECT * FROM events WHERE event_id=$event_id");
        $event = mysqli_fetch_assoc($res);
    } else {
        $msg = "Error: ".mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modify Event | Admin</title>

    <style>
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,.7), rgba(0,0,0,.9)),
                url("../public/images/bg1.jpg") center/cover no-repeat fixed;
            font-family:'Segoe UI',sans-serif;
            color:#fff;
            padding-top:80px;
        }

        .container{
            max-width:700px;
            margin:40px auto;
            background:rgba(255,255,255,0.08);
            padding:30px;
            border-radius:18px;
            backdrop-filter:blur(10px);
        }

        h2{text-align:center;margin-bottom:20px;}

        label{
            font-weight:600;
            margin-top:12px;
            display:block;
        }

        input, textarea, select{
            width:100%;
            padding:12px;
            margin-top:6px;
            border-radius:10px;
            border:none;
            outline:none;
        }

        button{
            width:100%;
            margin-top:25px;
            padding:14px;
            border:none;
            border-radius:30px;
            background:linear-gradient(135deg,#ff7a18,#ffb347);
            font-weight:600;
            cursor:pointer;
        }

        .msg{
            text-align:center;
            margin-bottom:15px;
            color:#4caf50;
            font-weight:600;
        }

        img{
            width:100%;
            height:200px;
            object-fit:cover;
            border-radius:12px;
            margin-top:10px;
        }
    </style>
</head>
<body>

<?php include("../templates/navbar.php"); ?>

<div class="container">
    <h2>Modify Event</h2>

    <?php if($msg!=""){ ?>
        <div class="msg"><?php echo $msg; ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>

        <label>Description</label>
        <textarea name="description" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>

        <label>Category</label>
        <select name="category">
            <?php
            $cats = array("Technical","Cultural","Sports","Workshop","Seminar","Social","Exhibition");
            foreach($cats as $c){
                $sel = ($event['category']==$c)?"selected":"";
                echo "<option $sel>$c</option>";
            }
            ?>
        </select>

        <label>Date</label>
        <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>">

        <label>Venue</label>
        <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>">

        <label>Registration Fee</label>
        <input type="number" step="0.01" name="registration_fee" value="<?php echo $event['registration_fee']; ?>">

        <label>Status</label>
        <select name="status">
            <option value="pending"  <?php if($event['status']=='pending') echo "selected"; ?>>Pending</option>
            <option value="approved" <?php if($event['status']=='approved') echo "selected"; ?>>Approved</option>
            <option value="rejected" <?php if($event['status']=='rejected') echo "selected"; ?>>Rejected</option>
        </select>

        <label>Event Image</label>
        <?php if($event['event_image']!=""){ ?>
            <img src="/EventHub_Sem6/public/<?php echo $event['event_image']; ?>">
        <?php } ?>
        <input type="file" name="event_image" accept="image/*">

        <label>Event File</label>
        <input type="file" name="event_file">

        <button type="submit">Update Event</button>
    </form>
</div>

</body>
</html>
