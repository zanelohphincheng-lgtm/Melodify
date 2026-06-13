<?php
require("header.php");

$insert_query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['role'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    if ($password == $confirm_password) {
        $stmt = $db->prepare($insert_query);
        $stmt->execute([
            ":username" => $username,
            ":email" => $email,
            ":password" => password_hash($password, PASSWORD_BCRYPT),
            ":role" => $role
        ]);
        // 🌟 The JavaScript Alert + Redirect Trick
        echo "<script>
            alert('User \"' + " . json_encode($username) . " + '\" has been successfully added!');
            window.location.href = 'manage-users.php';
        </script>";
        exit(); // Stop any further PHP execution
        
    } else {
        echo "<script>alert('Passwords do not match! Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Add New User</title>
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

        .add-container {
            width: 100%;
            max-width: 750px;
        }

        /* Glassmorphic Base Deck Styling Card Structure */
        .add-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .panel-title {
            font-size: 1.8rem;
            font-weight: 700;
        }

        /* Navpill Blue pill back link widget button */
        .btn-pill-back {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            text-decoration: none;
            background: linear-gradient(90deg, #729fff, #0953ca);
            color: white;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-pill-back:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        }

        /* Input labels style typography match */
        .form-label-custom {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #ffffff;
        }

        /* Solid pure white form inputs layout structure reference */
        .form-control-custom {
            background-color: #ffffff !important;
            color: #0b0f19 !important;
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1.1rem;
            font-weight: 500;
        }
        .form-control-custom:focus {
            box-shadow: 0 0 0 3px rgba(217, 70, 239, 0.4);
        }

        /* Custom white selector node element frame block */
        .form-select-custom {
            background-color: #ffffff !important;
            color: #0b0f19 !important;
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Vivid purple system gradient processing button module blueprint */
        .btn-submit-custom {
            background: linear-gradient(90deg, #2563eb, #d946ef);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.2rem;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
            margin-top: 15px;
        }
        .btn-submit-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.5);
            color: white;
        }
    </style>
</head>
<body>

    <div class="add-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4 px-1">
            <a href="manage-users.php" class="btn-pill-back">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
            <div class="panel-title text-center flex-grow-1 me-5">
                <img src="upload/logo3.png" alt="Logo" width="32" class="me-2 mb-1">
                Add New User
            </div>
        </div>

        <div class="add-card">
            <form method="POST" action="">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Username</label>
                        <input type="text" name="username" class="form-control form-control-custom" 
                               placeholder="Enter Username" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Email</label>
                        <input type="email" name="email" class="form-control form-control-custom" 
                               placeholder="name@email.com" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Password</label>
                        <input type="password" name="password" class="form-control form-control-custom" 
                               placeholder="-" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-custom" 
                               placeholder="-" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label form-label-custom">Role</label>
                    <select name="role" class="form-select form-select-custom">
                        <option value="2" selected>2 (User)</option>
                        <option value="1">1 (Admin)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-submit-custom">Add New User</button>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>