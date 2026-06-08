<?php
session_start();

if (isset($_POST['username'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    $db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");

    $query = "INSERT INTO `users` (username, email, password, role) VALUES (:username, :email, :password, :role)";

    $stmt = $db->prepare($query);
    $success = $stmt->execute(array(
        ':username'=>$username,
        ':email'=>$email,
        ':password'=>password_hash($password, PASSWORD_BCRYPT),
        ':role'=>2
    ));
    
    if ($success) {
        //Fetch the newly created user data from the DB
        $searchQuery = "SELECT * FROM users WHERE username = :username";
        $searchStmt = $db->prepare($searchQuery);
        $searchStmt->execute(array(':username' => $username));
        $newUser = $searchStmt->fetch(PDO::FETCH_ASSOC);

        //Log them in automatically by saving them to the active session tracker!
        $_SESSION['user'] = $newUser;

        //Redirect straight to the dashboard with active login status
        header("Location: index.php");
        exit();
    } else {
        echo "Registration failed database execution.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Sign Up Success</title>
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
        }

        /* Centered Glass Card Container */
        .success-card {
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

        /* Brand Title Typography */
        .brand-title {
            font-family: 'Arial Rounded MT Bold', 'Comic Sans MS', sans-serif;
            font-weight: bold;
        }

        /* Purple/Blue Gradient Action Button */
        .btn-gradient-custom {
            background: linear-gradient(90deg, #2563eb, #d946ef);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
        }

        .btn-gradient-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.4);
            color: white;
        }

        /* Red Logout/Exit Button */
        .btn-danger-custom {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            transition: background-color 0.2s, transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-danger-custom:hover {
            background-color: #dc2626;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="success-card text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
            <img src="upload/logo3.png" alt="Melodify Logo" width="42" height="42">
            <h2 class="m-0 brand-title fst-italic">Melodify</h2>
        </div>

        <h3 class="fw-bold mb-1">Sign Up Success!</h3>
        <p class="text-light opacity-75 mb-4">Welcome <?= htmlspecialchars($username) ?></p>

        <div class="d-grid gap-3">
            <a href="index.php" class="btn btn-gradient-custom">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
            
            <div>
                <a href="logout.php" class="btn btn-danger-custom">Log Out</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>