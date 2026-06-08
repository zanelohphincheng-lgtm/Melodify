<?php
session_start();

$username = isset($_POST['username']) ? $_POST['username'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

// Initialize status flags
$login_attempted = false;
$login_success = false;

if (isset($_POST['username'])) {
    $login_attempted = true;
    
    // Connect to your database
    $db = new PDO("mysql:host=localhost;dbname=project_sem1", "root", "");

    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->execute(array(':username' => $username));
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $is_password_match = password_verify($password, $user['password']);
    } else {
        $is_password_match = false;
    }

    if ($is_password_match) {
        $_SESSION['user'] = $user;
        $login_success = true;
    } else {
        // FAIL TRIGGER: Inject an instant native browser alert and kick them back to the form
        echo "<script>
            alert('Login Fail: Incorrect Username or Password');
            window.location.href = 'login-form.php';
        </script>";
        exit();
    }
} else {
    // If they landed here directly, check if they are already authenticated
    if (isset($_SESSION['user'])) {
        $login_success = true;
    } else {
        header("Location: login-form.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Authentication State</title>
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

        /* Top Green Indicator Bar */
        .status-bar {
            width: 100%;
            max-width: 440px;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            background-color: #15803d; /* Rich green */
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Glassmorphic Core Container Card */
        .auth-card {
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

        .btn-dashboard {
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

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.4);
            color: white;
        }

        .btn-logout-red {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: block;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn-logout-red:hover {
            background-color: #dc2626;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <?php if ($login_attempted && $login_success): ?>
        <div class="status-bar">
            <i class="bi bi-check-circle"></i> Logged in successfully
        </div>
    <?php endif; ?>

    <div class="auth-card text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
            <img src="upload/logo3.png" alt="Melodify Logo" width="42" height="42">
            <h2 class="m-0 brand-title fst-italic">Melodify</h2>
        </div>

        <h3 class="mb-4 fw-bold">
            <?php if ($login_attempted && $login_success): ?>
                Logged in as <span class="text-info"><?= htmlspecialchars($_SESSION['user']['username']); ?></span>
            <?php else: ?>
                Already Logged In
            <?php endif; ?>
        </h3>

        <div class="d-grid gap-3">
            <a href="index.php" class="btn btn-dashboard">Go to Homepage</a>
            <a href="logout.php?logout=true" class="btn btn-logout-red">Log Out</a>
        </div>
    </div>

</body>
</html>