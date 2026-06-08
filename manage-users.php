<?php
session_start();

// SECURITY GUARD: Ensure only logged-in administrators can view this control panel!
// (Assuming role 1 = Admin, role 2 = User. Adjust according to your database values)
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

// 🚫 ACTION TRIGGER: Handle User Deletion if a delete request is fired
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Prevent the logged-in admin from accidentally deleting themselves!
    if ($delete_id !== intval($_SESSION['user']['id'])) {
        $delete_query = "DELETE FROM users WHERE id = :id";
        $delete_stmt = $db->prepare($delete_query);
        $delete_stmt->execute([':id' => $delete_id]);
    }
    header("Location: manage-users.php");
    exit();
}

// FETCH DATA: Retrieve all current user records ordered by newest ID first
$query = "SELECT id, username, email, role FROM users ORDER BY id DESC";
$stmt = $db->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Manage Users</title>
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

        /* Matches your wide dashboard layout frame */
        .admin-container {
            width: 100%;
            max-width: 900px;
        }

        /* Glassmorphic Core Card Styling */
        .management-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        /* Top Title Font Architecture */
        .panel-title {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Styled Action Buttons matching your canvas mockups */
        .btn-pill-custom {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.2s, background-color 0.2s;
        }

        .btn-blue-pill {
            background: linear-gradient(90deg, #729fff, #0953ca);
            color: white;
        }
        .btn-blue-pill:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-purple-pill {
            background: linear-gradient(90deg, #a855f7, #d946ef);
            color: white;
        }
        .btn-purple-pill:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 15px rgba(217, 70, 239, 0.3);
        }

        /* Custom Table Structure overrides for clear glass styling */
        .custom-table {
            color: #ffffff !important;
            margin-bottom: 0;
            vertical-align: middle;
        }
        
        .custom-table thead th {
            border-bottom: 2px solid rgba(255, 255, 255, 0.15);
            font-size: 1.1rem;
            font-weight: 600;
            padding-bottom: 15px;
            color: #94a3b8;
        }

        .custom-table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .custom-table tbody td {
            padding: 15px 10px;
            font-size: 1.05rem;
        }

        /* Role Pill Badges matching layout colors */
        .badge-role {
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
            text-transform: capitalize;
        }

        .badge-admin {
            background-color: #3a92b7; 
            color: #ffffff;
        }

        .badge-user {
            background-color: #8f5cb7;
            color: #ffffff;
        }

        /* Square Icon Control Buttons Grid */
        .action-box {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0b0f19;
            font-size: 1rem;
            transition: transform 0.2s, opacity 0.2s;
            border: none;
        }
        .action-box:hover {
            transform: scale(1.1);
            color: #0b0f19;
        }

        .bg-box-edit { background-color: #22c55e; }     /* Green */
        .bg-box-delete { background-color: #ef4444; }   /* Red */
    </style>
</head>
<body>

    <div class="admin-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4 w-100 px-1">
            <a href="dashboard.php" class="btn-pill-custom btn-blue-pill">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
            <div class="panel-title text-center">
                <img src="upload/logo3.png" alt="Logo" width="30" class="me-2 mb-1">
                Manage Users
            </div>
            <a href="manage-users-add.php" class="btn-pill-custom btn-purple-pill">
                Add New User <i class="bi bi-person-plus-fill"></i> <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>

        <div class="management-card">
            <div class="table-responsive">
                <table class="table table-dark table-borderless custom-table">
                    <thead>
                        <tr>
                            <!-- Create spaces -->
                            <th style="width: 10%">ID</th>
                            <th style="width: 25%">Name</th>
                            <th style="width: 35%">Email</th>
                            <th style="width: 15%" class="text-center">Role</th>
                            <th style="width: 15%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']); ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($user['username']); ?></td>
                                <td class="text-white-50"><?= htmlspecialchars($user['email']); ?></td>
                                <td class="text-center">
                                    <?php if($user['role'] == "admin"): ?>
                                        <span class="badge-role badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge-role badge-user">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="manage-users-edit.php?id=<?= $user['id']; ?>" class="action-box bg-box-edit" title="Edit Profile">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="manage-users.php?delete_id=<?= $user['id']; ?>" 
                                           class="action-box bg-box-delete" 
                                           title="Delete User"
                                           onclick="return confirm('Are you sure you want to completely remove <?= htmlspecialchars($user['username']); ?>? This cannot be undone.');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>