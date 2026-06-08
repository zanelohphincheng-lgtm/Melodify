<?php
session_start();

// SECURITY GUARD: Only allow logged-in administrators
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != "admin") {
    header("Location: index.php");
    exit();
}

// Database Connection
try {
    $db = new PDO("mysql:host=localhost;dbname=project_sem1", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// 1. READ PHASE: Get user data to populate the form fields automatically
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If user doesn't exist, kick back to management table
    if (!$user) {
        header("Location: manage-users.php");
        exit();
    }
} else {
    header("Location: manage-users.php");
    exit();
}

// 2. UPDATE PHASE: Handle form submission values
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $role = isset($_POST['role']) ? intval($_POST['role']) : 2;

    if (!empty($username) && !empty($email)) {
        try {
            $update_query = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
            $update_stmt = $db->prepare($update_query);
            $success = $update_stmt->execute([
                ':username' => $username,
                ':email'    => $email,
                ':role'     => $role,
                ':id'       => $user_id
            ]);

            if ($success) {
                // If the admin edited their own active account profile, update their live session values!
                if ($user_id == $_SESSION['user']['id']) {
                    $_SESSION['user']['username'] = $username;
                    $_SESSION['user']['email'] = $email;
                    $_SESSION['user']['role'] = $role;
                }
                
                echo "<script>
                    alert('User updated successfully!');
                    window.location.href = 'manage-users.php';
                </script>";
                exit();
            }
        } catch (PDOException $e) {
            echo "<script>alert('Error updating user: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill out all fields.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Edit User</title>
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

        .edit-container {
            width: 100%;
            max-width: 750px;
        }

        /* Glassmorphic Edit Card Frame matching layout reference */
        .edit-card {
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

        /* Navpill link button layout style */
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
            box-shadow: 0 4px 15px rgba(217, 70, 239, 0.3);
        }

        /* Form labels architecture matching input elements */
        .form-label-custom {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #ffffff;
        }

        /* Flat white input styling block matches mockup designs */
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
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.4);
        }

        /* Sleek custom dark dropdown background selection menu layout */
        .form-select-custom {
            background-color: #ffffff !important;
            color: #0b0f19 !important;
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Massive purple update grid width execution component template */
        .btn-update-custom {
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
        .btn-update-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.5);
            color: white;
        }
    </style>
</head>
<body>

    <div class="edit-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4 px-1">
            <a href="manage-users.php" class="btn-pill-back">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
            <div class="panel-title text-center flex-grow-1 me-5">
                <img src="upload/logo3.png" alt="Logo" width="32" class="me-2 mb-1">
                Edit User : <span class="text-info"><?= htmlspecialchars($user['username']); ?></span>
            </div>
        </div>

        <div class="edit-card">
            <form method="POST" action="">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Username</label>
                        <input type="text" name="username" class="form-control form-control-custom" 
                               value="<?= htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Email</label>
                        <input type="email" name="email" class="form-control form-control-custom" 
                               value="<?= htmlspecialchars($user['email']); ?>" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label form-label-custom">Role</label>
                    <select name="role" class="form-select form-select-custom">
                        <option value="1" <?= $user['role'] == "admin" ? 'selected' : ''; ?>>Admin</option>
                        <option value="2" <?= $user['role'] == "user" ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-update-custom">Update</button>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>