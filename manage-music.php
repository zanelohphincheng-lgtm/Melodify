<?php
require("header.php");

// 🚫 ACTION TRIGGER: Handle song Deletion if a delete request is fired
if(isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Prevent the logged-in admin from accidentally deleting themselves!
    if ($delete_id !== intval($_SESSION['song']['id'])) {
        $delete_query = "DELETE FROM songs WHERE id = :id";
        $delete_stmt = $db->prepare($delete_query);
        $delete_stmt->execute([':id' => $delete_id]);
    }
    header("Location: manage-songs.php");
    exit();
}

// FETCH DATA: Retrieve all current song records ordered by newest ID first
$query = "SELECT songs.id, songs.song_name, songs.duration, songs.uploadDate,songs.album_id, artists.artist_name FROM songs INNER JOIN artists ON songs.artist_id = artists.id  ORDER BY id DESC";
$stmt = $db->query($query);
$songs = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Manage Songs</title>
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

        /* duration Pill Badges matching layout colors */
        .badge-duration {
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

        .badge-song {
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

        .bg-box-view { background-color: #00ccfa; }     /* Blue */
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
                Manage Songs
            </div>
            <a href="manage-songs-add.php" class="btn-pill-custom btn-purple-pill">
                Add New Song <i class="bi bi-person-plus-fill"></i> <i class="bi bi-arrow-right-circle"></i>
            </a>
        </div>

        <div class="management-card">
            <div class="table-responsive">
                <table class="table table-dark table-borderless custom-table">
                    <thead>
                        <tr>
                            <!-- Create spaces -->
                            <th style="width: 10%">ID</th>
                            <th style="width: 30%">Name</th>
                            <th style="width: 20%">Artist</th>
                            <th style="width: 5%" class="text-center">Duration</th>
                            <th style="width: 10%" class="text-center">UploadDate</th>
                            <th style="width: 15%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($songs as $song): ?>
                            <tr>
                                <td><?= htmlspecialchars($song['id']); ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($song['song_name']); ?></td>
                                <td class="text-white"><?= htmlspecialchars($song['artist_name']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($song['duration']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($song['uploadDate']); ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="album.php?id=<?= $song['album_id']; ?>" class="action-box bg-box-view" title="View Song">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="manage-songs-edit.php?id=<?= $song['id']; ?>" class="action-box bg-box-edit" title="Edit Song">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="manage-songs.php?delete_id=<?= $song['id']; ?>" 
                                           class="action-box bg-box-delete" 
                                           title="Delete song"
                                           onclick="return confirm('Are you sure you want to completely remove <?= htmlspecialchars($song['song_name']); ?>? This cannot be undone.');">
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