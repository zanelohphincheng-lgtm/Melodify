<?php
session_start();

// Initialize our status message flags
$show_logged_out_success = false;
$show_not_signed_in_error = false;

// Case 1: The user explicitly clicked a real logout action button
if (isset($_GET['logout']) && $_GET['logout'] == "true") {
    if (isset($_SESSION['user'])) {
        // They were logged in, so clear everything out cleanly
        $_SESSION = array();
        session_destroy();
        $show_logged_out_success = true; // This triggers your GREEN bar
    } else {
        // They triggered a logout action but didn't even have an active session locker
        $show_not_signed_in_error = true; // This triggers your RED bar
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify | Logout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #0b0f19;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        /* Status Bar Styles matching your screenshots */
        .status-bar {
            width: 100%;
            max-width: 440px;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .status-success {
            background-color: #198754;/* Bootstrap Success color code */
            color: #ffffff;
        }

        .status-error {
            background-color: #dc3545; /* Bootstrap Danger color code */
            color: #ffffff;
        }

        /* Core Glass Card Layout Container */
        .logout-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .brand-title {
            font-family: 'Arial Rounded MT Bold', sans-serif;
            font-weight: bold;
        }

        .btn-gradient-custom {
            background: linear-gradient(90deg, #2563eb, #d946ef);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: block;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-gradient-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.4);
            color: white;
        }

        .btn-back-custom {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn-back-custom:hover {
            background-color: #dc2626;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <?php if ($show_logged_out_success): ?>
        <div class="status-bar status-success">
            <i class="bi bi-check-circle"></i> You are logged out
        </div>
    <?php elseif ($show_not_signed_in_error): ?>
        <div class="status-bar status-error">
            <i class="bi bi-x-circle"></i> You are not signed in
        </div>
    <?php endif; ?>

    <div class="logout-card text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
            <img src="upload/logo3.png" alt="Melodify Logo" width="42" height="42">
            <h2 class="m-0 brand-title fst-italic">Melodify</h2>
        </div>

        <div class="d-grid gap-3 mb-4">
            <a href="login-form.php" class="btn btn-gradient-custom">Login</a>
            <a href="register-form.php" class="btn btn-gradient-custom">Sign Up</a>
        </div>

        <div class="pt-2">
            <a href="index.php" class="btn btn-back-custom">
                <i class="bi bi-arrow-left-circle"></i> Go To Homepage
            </a>
        </div>
    </div>

</body>
</html>