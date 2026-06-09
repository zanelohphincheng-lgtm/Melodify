<?php
session_start();

// Database Connection
$db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");

// FORM ACTION SUBMISSION HANDLER
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // 1. Check for empty inputs
    if (empty($username) || empty($email) || empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('Please fill out all fields.');</script>";
    } 
    // 2. Validate password match
    elseif ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match! Please check again.');</script>";
    } else {
        try {
            // 3. Verify if the account exists with BOTH matching credentials
            $verify_query = "SELECT * FROM users WHERE username = :username AND email = :email";
            $verify_stmt = $db->prepare($verify_query);
            $verify_stmt->execute([
                ':username' => $username,
                ':email'    => $email
            ]);
            $user = $verify_stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // 4. Match found! Hash the new password and update the database record
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                
                $update_query = "UPDATE users SET password = :password WHERE id = :id";
                $update_stmt = $db->prepare($update_query);
                $success = $update_stmt->execute([
                    ':password' => $hashed_password,
                    ':id'       => $user['id']
                ]);

                if ($success) {
                    echo "<script>
                        alert('Password updated successfully! Redirecting to login page...');
                        window.location.href = 'login-form.php'; 
                    </script>";
                    exit();
                }
            } else {
                // Security tip: Keeping this message generic prevents brute-force username scanning
                echo "<script>alert('Invalid account details. Username and Email combo does not match our records.');</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Error processing update: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Change Password</title>
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

        .recovery-container {
            width: 100%;
            max-width: 750px;
        }

        /* Glassmorphic Frame matching your design system specifications */
        .recovery-card {
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

        /* Navpill Blue link back button style asset matching user-add file */
        .btn-pill-back {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            text-decoration: none;
            background: #2563eb;
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

        /* Custom Input label typography blocks */
        .form-label-custom {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #ffffff;
        }

        /* Flat solid layout white form inputs matches image token specs */
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

        /* Centered gradient submit button structure blueprint layout */
        .btn-submit-center {
            background: linear-gradient(90deg, #2563eb, #d946ef);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 1.15rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(217, 70, 239, 0.3);
        }
        .btn-submit-center:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.5);
            color: white;
        }
    </style>
</head>
<body>

    <div class="recovery-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4 px-1">
            <a href="login-form.php" class="btn-pill-back">
                <i class="bi bi-arrow-left-circle-fill"></i> Go Back
            </a>
            <div class="panel-title text-center flex-grow-1 me-5">
                <img src="upload/logo3.png" alt="Logo" width="32" class="me-2 mb-1">
                Change Password
            </div>
        </div>

        <div class="recovery-card">
            <form method="POST" action="">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Username</label>
                        <input type="text" name="username" class="form-control form-control-custom" 
                               placeholder="Enter your username" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Email</label>
                        <input type="email" name="email" class="form-control form-control-custom" 
                               placeholder="Enter your registered email" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">New Password</label>
                        <input type="password" name="new_password" class="form-control form-control-custom" 
                               placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-custom" 
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-submit-center">Update Password</button>
                </div>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>