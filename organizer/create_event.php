<?php
session_start();
include("../config/db.php");

/* SECURITY: Only organizer allowed */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../auth/login.php");
    exit();
}

$msg = "";

/* HANDLE FORM SUBMIT */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $event_date  = $_POST['event_date'];
    $venue       = mysqli_real_escape_string($conn, $_POST['venue']);
    $fee         = $_POST['registration_fee'];
    $created_by  = $_SESSION['user_id'];

    /* FILE UPLOAD: EVENT FILE */
    $event_file = "";
    if (!empty($_FILES['event_file']['name'])) {
        $folder = "../public/uploads/files/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $event_file = time() . "_" . basename($_FILES['event_file']['name']);
        move_uploaded_file($_FILES['event_file']['tmp_name'], $folder . $event_file);
        $event_file = "uploads/files/" . $event_file;
    }

    /* FILE UPLOAD: EVENT IMAGE */
    $event_image = "";
    if (!empty($_FILES['event_image']['name'])) {
        $imgFolder = "../public/uploads/images/";
        if (!is_dir($imgFolder)) {
            mkdir($imgFolder, 0777, true);
        }
        $event_image = time() . "_" . basename($_FILES['event_image']['name']);
        move_uploaded_file($_FILES['event_image']['tmp_name'], $imgFolder . $event_image);
        $event_image = "uploads/images/" . $event_image;
    }

    /* INSERT QUERY */
    $sql = "INSERT INTO events
        (title, description, category, event_date, venue, registration_fee, event_file, event_image, created_by, status)
        VALUES
        ('$title', '$description', '$category', '$event_date', '$venue', '$fee', '$event_file', '$event_image', '$created_by', 'pending')";

    if (mysqli_query($conn, $sql)) {
        $msg = "Event submitted successfully. Waiting for admin approval.";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Event | Organizer</title>

    <style>
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.9)),
                url('../public/uploads/images/bg4.jpg') center/cover no-repeat fixed;
            font-family:'Segoe UI',sans-serif;
        }

        .container{
            max-width:650px;
            margin:120px auto;
            background:rgba(255,255,255,0.95);
            padding:30px;
            border-radius:16px;
            box-shadow:0 20px 40px rgba(0,0,0,0.3);
        }

        h2{
            text-align:center;
            margin-bottom:20px;
            color:#333;
        }

        label{
            font-weight:600;
            margin-top:12px;
            display:block;
            color:#333;
        }

        input, textarea, select{
            width:100%;
            padding:12px;
            margin-top:6px;
            border-radius:10px;
            border:1px solid #ccc;
            font-size:14px;
        }

        input[type="file"]{
            padding:8px;
        }

        button{
            width:100%;
            margin-top:20px;
            padding:14px;
            border:none;
            border-radius:30px;
            background:linear-gradient(135deg,#ff7a18,#ffb347);
            color:#000;
            font-size:16px;
            font-weight:600;
            cursor:pointer;
        }

        .msg{
            text-align:center;
            margin-bottom:15px;
            color:green;
            font-weight:600;
        }
    </style>
</head>
<body>

<?php include("../templates/navbar.php"); ?>

<div class="container">
    <h2>Create New Event</h2>

    <?php if($msg != ""){ ?>
        <div class="msg"><?php echo $msg; ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Event Title</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Category</label>
        <select name="category" required>
            <option value="">Select Category</option>
            <option>Technical</option>
            <option>Cultural</option>
            <option>Sports</option>
            <option>Workshop</option>
            <option>Seminar</option>
        </select>

        <label>Event Date</label>
        <input type="date" name="event_date" required>

        <label>Venue</label>
        <input type="text" name="venue" required>

        <label>Registration Fee (₹)</label>
        <input type="number" step="0.01" name="registration_fee" value="0">

        <label>Event Image</label>
        <input type="file" name="event_image" accept="image/*">

        <label>Event File (PDF / DOC)</label>
        <input type="file" name="event_file">

        <button type="submit">Submit Event</button>
    </form>
</div>

</body>
</html>
