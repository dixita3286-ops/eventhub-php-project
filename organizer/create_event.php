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

    /* FILE UPLOAD */
    $event_file = "";
    if (!empty($_FILES['event_file']['name'])) {
        $folder = "../public/uploads/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $event_file = time() . "_" . basename($_FILES['event_file']['name']);
        move_uploaded_file($_FILES['event_file']['tmp_name'], $folder . $event_file);
    }

    /* INSERT QUERY */
    $sql = "INSERT INTO events
        (title, description, category, event_date, venue, registration_fee, event_file, created_by, status)
        VALUES
        ('$title', '$description', '$category', '$event_date', '$venue', '$fee', '$event_file', '$created_by', 'pending')";

    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Event submitted successfully. Waiting for admin approval.";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Event | Organizer</title>

    <style>
        body{
            background:#f4f6f9;
            font-family:'Segoe UI',sans-serif;
        }

        .container{
            max-width:600px;
            margin:120px auto;
            background:#fff;
            padding:25px;
            border-radius:10px;
        }

        h2{
            text-align:center;
            margin-bottom:20px;
        }

        input, textarea, select, button{
            width:100%;
            padding:10px;
            margin:8px 0;
        }

        button{
            background:#ff7a18;
            border:none;
            color:#fff;
            font-size:16px;
            cursor:pointer;
        }

        .msg{
            text-align:center;
            margin-bottom:10px;
            color:green;
        }
    </style>
</head>
<body>

<?php include("../templates/navbar.php"); ?>

<div class="container">
    <h2>Create New Event</h2>

    <?php if($msg!=""){ ?>
        <div class="msg"><?= $msg ?></div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="title" placeholder="Event Title" required>

        <textarea name="description" placeholder="Event Description" rows="4" required></textarea>

        <select name="category" required>
            <option value="">Select Category</option>
            <option>Technical</option>
            <option>Cultural</option>
            <option>Sports</option>
            <option>Workshop</option>
            <option>Seminar</option>
        </select>

        <input type="date" name="event_date" required>

        <input type="text" name="venue" placeholder="Venue" required>

        <input type="number" step="0.01" name="registration_fee" placeholder="Registration Fee" value="0">

        <input type="file" name="event_file">

        <button type="submit">Submit Event</button>
    </form>
</div>

</body>
</html>
