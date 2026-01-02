<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>About Us | EventHub</title>

    <style>
        body{
            min-height:100vh;
            background:
                linear-gradient(to bottom, rgba(0,0,0,.8), rgba(0,0,0,.95)),
                url("public/images/bg1.jpg") center/cover no-repeat fixed;
            color:#fff;
            font-family:'Segoe UI',sans-serif;
        }

        .container{
            max-width:1000px;
            margin:130px auto 60px;
            padding:0 20px;
        }

        h1{
            text-align:center;
            margin-bottom:30px;
            color:#ffb347;
        }

        .card{
            background:rgba(255,255,255,0.08);
            border-radius:18px;
            padding:30px;
            backdrop-filter:blur(12px);
        }

        p{
            font-size:16px;
            line-height:1.7;
            color:#ddd;
            margin-bottom:18px;
        }

        .features{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
            gap:20px;
            margin-top:30px;
        }

        .feature-box{
            background:rgba(0,0,0,0.4);
            padding:20px;
            border-radius:14px;
        }

        .feature-box h3{
            margin-bottom:10px;
            color:#ff7a18;
        }

        .feature-box p{
            font-size:14px;
        }
    </style>
</head>
<body>

<?php include("templates/navbar.php"); ?>

<div class="container">

    <h1>About EventHub</h1>

    <div class="card">

        <p>
            <strong>EventHub</strong> is a College Event Management System designed to
            simplify the process of organizing, managing, and participating in
            college events. It provides a centralized digital platform for students,
            organizers, and administrators.
        </p>

        <p>
            The system ensures transparency and efficiency by allowing organizers
            to create events, administrators to approve and manage them, and students
            to easily discover and register for events.
        </p>

        <p>
            EventHub was developed as a <strong>BCA Semester 6 project</strong> using
            PHP and MySQL, focusing on real-world application design, role-based access
            control, and secure data handling.
        </p>

        <div class="features">

            <div class="feature-box">
                <h3>🎯 Role-Based Access</h3>
                <p>
                    Separate dashboards for Admin, Organizer, and Student to ensure
                    controlled and secure access.
                </p>
            </div>

            <div class="feature-box">
                <h3>📅 Event Management</h3>
                <p>
                    Organizers can create events while admins approve or reject them
                    before publishing.
                </p>
            </div>

            <div class="feature-box">
                <h3>📝 Easy Registration</h3>
                <p>
                    Students can browse events, view details, and register with ease.
                </p>
            </div>

            <div class="feature-box">
                <h3>📊 Reports & Tracking</h3>
                <p>
                    Admins and organizers can track participation and event performance.
                </p>
            </div>

        </div>

    </div>

</div>

</body>
</html>
