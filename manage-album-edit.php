<?php
require("header.php"); // Ensure your PDO connection variable ($db) is defined here

$message = "";
$messageType = "info"; // Used to toggle Bootstrap alert styles dynamically

// 1. READ PHASE: Get album data to populate the form fields automatically
if (isset($_GET['id'])) {
    $album_id = intval($_GET['id']);
    
    $query = "SELECT * FROM album WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $album_id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If album doesn't exist, kick back to management table
    if (!$album) {
        header("Location: manage-album.php");
        exit();
    }
} else {
    header("Location: manage-album.php");
    exit();
}

// FIX 1: Explicitly fetch ALL available artists so we can build the select dropdown correctly
try {
    $artist_list_query = "SELECT id, artist_name FROM artists ORDER BY artist_name ASC";
    $artist_list_stmt = $db->query($artist_list_query);
    $artists = $artist_list_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $artists = [];
}

// 2. UPDATE PHASE: Handle form submission values
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "upload/";

    // Create the upload folder automatically if it doesn't exist yet
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Gather and trim all textual inputs from the form
    $album_name   = isset($_POST['album_name']) ? trim($_POST['album_name']) : '';
    $artist_id    = isset($_POST['artist_id']) ? trim($_POST['artist_id']) : '';
    $debut        = isset($_POST['debut']) ? trim($_POST['debut']) : '';
    $type         = isset($_POST['type']) ? trim($_POST['type']) : '';

    // FIX 3: Point the fallback back to the correct database container array ($album, not $artist)
    $artist_image_path = $album['artist_image'];
    $cover_image_path = $album['cover_image'];
    $uploadOk = 1;

    // Validate and process album Profile Picture Upload (Only if user selected a file)
    if (isset($_FILES['artist_image']) && $_FILES['artist_image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES["artist_image"]["name"], PATHINFO_EXTENSION));
        if ($ext === "png") {
            $artist_image_name = time() . "_" . basename($_FILES["artist_image"]["name"]); 
            $artist_image_path = $target_dir . $artist_image_name;
            
            if (!move_uploaded_file($_FILES["artist_image"]["tmp_name"], $artist_image_path)) {
                $message .= "Failed to move Artist Image file onto server. ";
                $uploadOk = 0;
            }
        } else {
            $message .= "Sorry, only PNG files are allowed for Artist Image. ";
            $uploadOk = 0;
        }
    }

    // Validate and process Wide Billboard Cover Upload (Only if user selected a file)
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES["cover_image"]["name"], PATHINFO_EXTENSION));
        if ($ext === "png") {
            $cover_image_name = time() . "_cover_" . basename($_FILES["cover_image"]["name"]); 
            $cover_image_path = $target_dir . $cover_image_name;
            
            if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $cover_image_path)) {
                $message .= "Failed to move Cover Image file onto server. ";
                $uploadOk = 0;
            }
        } else {
            $message .= "Sorry, only PNG files are allowed for Cover Image. ";
            $uploadOk = 0;
        }
    }

    // FIX 2: Removed the broken $_POST image checks out of this empty field filter check completely!
    if ($uploadOk == 1 && !empty($album_name) && !empty($artist_id) && !empty($debut) && !empty($type)) {
        try {
            $update_query = "UPDATE album SET
                             album_name = :album_name,
                             artist_id = :artist_id,
                             debut = :debut,
                             artist_image = :artist_image,
                             cover_image = :cover_image,
                             type = :type
                             WHERE id = :id";
            
            $update_stmt = $db->prepare($update_query);
            $success = $update_stmt->execute([
                ':album_name'   => $album_name,
                ':artist_id'    => $artist_id,
                ':debut'        => $debut,
                ':artist_image' => $artist_image_path,
                ':cover_image'  => $cover_image_path,
                ':type'         => $type,
                ':id'           => $album_id
            ]);

            if ($success) {
                echo "<script>
                    alert('Album updated successfully!');
                    window.location.href = 'manage-album.php';
                </script>";
                exit();
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageType = "danger";
        }
    } else {
        $messageType = "danger";
        if (empty($message)) {
            $message = "Please fill out all text fields.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Edit Album</title>
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
        .add-container { width: 100%; max-width: 750px; }
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
        .btn-pill-back {
            border-radius: 20px; 
            padding: 8px 20px; 
            font-weight: 600; 
            text-decoration: none;
            background: linear-gradient(90deg, #729fff, #0953ca); 
            color: white;
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            transition: 0.2s;
        }
        .btn-pill-back:hover { 
            transform: translateY(-2px); 
            color: white; 
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4); 
        }
        .form-label-custom { 
            font-size: 1.1rem; 
            font-weight: 600; 
            margin-bottom: 8px; 
            color: #ffffff; 
        }
        .form-control-custom, .form-select-custom {
            background-color: #ffffff !important; 
            color: #0b0f19 !important; 
            border: none;
            border-radius: 12px; 
            padding: 12px 16px; 
            font-size: 1.05rem; 
            font-weight: 500;
        }
        .form-control-custom:focus, .form-select-custom:focus { 
            box-shadow: 0 0 0 3px rgba(217, 70, 239, 0.4); 
            border: none; 
            outline: none; 
        }
        .btn-submit-custom {
            background: linear-gradient(90deg, #2563eb, #d946ef); 
            color: white; 
            border: none;
            padding: 14px; 
            border-radius: 15px; 
            font-weight: 700; 
            font-size: 1.2rem; 
            width: 100%; 
            margin-top: 15px; 
            transition: 0.2s;
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
            <a href="manage-album.php" class="btn-pill-back">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
            <div class="panel-title text-center flex-grow-1 me-5">
                <img src="upload/logo3.png" alt="Logo" width="32" class="me-2 mb-1">
                Edit Album
            </div>
        </div>

        <div class="add-card">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $messageType; ?> text-center border-0 fw-bold mb-4">
                    <?= htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Album Name</label>
                        <input type="text" name="album_name" class="form-control form-control-custom" value="<?= htmlspecialchars($album['album_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Artist</label>
                        <select name="artist_id" class="form-select form-select-custom" required>
                            <option value="" disabled>-- Select an Artist --</option>
                            <?php foreach ($artists as $row): ?>
                                <option value="<?= intval($row['id']); ?>" <?= ($row['id'] == $album['artist_id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($row['artist_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Artist Image (PNG Only)</label>
                        <input type="file" name="artist_image" class="form-control form-control-custom">
                        <small class="text-info d-block mt-1">Current: <?= basename($album['artist_image']); ?></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Cover Image (PNG Only)</label>
                        <input type="file" name="cover_image" class="form-control form-control-custom">
                        <small class="text-info d-block mt-1">Current: <?= basename($album['cover_image']); ?></small>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label form-label-custom">Release Debut Date</label>
                        <input type="text" name="debut" class="form-control form-control-custom" value="<?= htmlspecialchars($album['debut']); ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label form-label-custom">Type</label>
                    <select name="type" class="form-select form-select-custom" required>
                        <option value="album" <?= ($album['type'] === 'album') ? 'selected' : ''; ?>>Album</option>
                        <option value="single" <?= ($album['type'] === 'single') ? 'selected' : ''; ?>>Single</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-submit-custom">Update Album</button>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>