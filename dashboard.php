<?php
session_start();

// SECURITY CHECK: Kick out anyone who isn't logged in OR isn't an Admin (role 1)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != "admin") {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #0b0f19;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .dashboard-container {
            width: 100%;
            max-width: 850px;
            text-align: center;
        }

        /* Title Typography styling block matching mockup accents */
        .dashboard-title {
            font-size: 2.8rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 40px;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        /* Glassmorphic Side-by-Side Nav Decks layout token blueprint */
        .menu-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        /* Scale and glowing animation effect when hovering over menu selections */
        .menu-card:hover {
            transform: translateY(-8px);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
        }

        .card-heading {
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        /* Core Silhouette Icon Wrapper Blocks */
        .icon-box {
            background: rgba(11, 15, 25, 0.6);
            border-radius: 20px;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .icon-box i {
            font-size: 3rem;
            background: linear-gradient(135deg, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Access action navigation nodes layout system mapping */
        .btn-access {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 10px 32px;
            border-radius: 25px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }

        .btn-access:hover {
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        /* Bottom home landing layout exit button template frame */
        .btn-homepage-custom {
            background: linear-gradient(90deg, #2563eb, #d946ef);
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
            padding: 14px 45px;
            border-radius: 15px;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 5px 20px rgba(217, 70, 239, 0.2);
        }

        .btn-homepage-custom:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(217, 70, 239, 0.5);
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <div class="mb-2">
            <img src="upload/logo3.png" alt="Melodify Logo" width="55" class="mb-2">
        </div>
        <h1 class="dashboard-title">Admin Dashboard</h1>

        <div class="row g-4 justify-content-center mb-5">
            
            <div class="col-sm-6 col-md-5">
                <div class="menu-card">
                    <div class="card-heading">Manage Music List</div>
                    <div class="icon-box">
                        <i class="bi bi-music-note-beamed"></i>
                    </div>
                    <a href="manage-music.php" class="btn-access">
                        <i class="bi bi-arrow-left-circle"></i> Access
                    </a>
                </div>
            </div>

            <div class="col-sm-6 col-md-5">
                <div class="menu-card">
                    <div class="card-heading">Manage Users</div>
                    <div class="icon-box">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <a href="manage-users.php" class="btn-access">
                        Access <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>

        </div>

        <div class="mt-4">
            <a href="index.php" class="btn-homepage-custom">
                <i class="bi bi-house-door-fill"></i> Go To Homepage
            </a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>