<?php
require("header.php"); // Ensure your PDO connection variable ($db) is defined here

$message = "";
$messageType = "info"; // Used to toggle Bootstrap alert styles dynamically

// FETCH ALL ARTISTS: Populate the relational selector dropdown element dynamically
try {
    $artist_query = "SELECT id, artist_name FROM artists ORDER BY artist_name ASC";
    $artist_stmt = $db->query($artist_query);
    $artists = $artist_stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error pulling artists configuration data: " . $e->getMessage();
    $messageType = "danger";
    $artists = [];
}

/**
 * Reusable Helper Function to Handle Image Uploads Safely
 * This eliminates duplicated code blocks entirely!
 */
function handleImageUpload($fileField, $target_dir, $prefix = "") {
    // If no file was uploaded or there was an upload error, return an empty string
    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== 0) {
        return "";
    }

    $filename = $_FILES[$fileField]['name'];
    $tempPath = $_FILES[$fileField]['tmp_name'];
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Enforce file type check
    if ($extension !== "png") {
        return "ERROR: Only PNG files are allowed.";
    }

    // Build a unique safe filename using a timestamp
    $cleanName = time() . "_" . $prefix . basename($filename);
    $finalPath = $target_dir . $cleanName;

    // Try to move the file onto the server storage folder
    if (move_uploaded_file($tempPath, $finalPath)) {
        return $finalPath; // Return the saved file path string
    }

    return "ERROR: Failed to save file onto the server.";
}


// PROCESS FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "upload/";
    
    // Create the upload directory automatically if missing
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // 1. Gather and Clean Text Values Clearly
    $album_name = isset($_POST['album_name']) ? trim($_POST['album_name']) : '';
    $artist_id  = isset($_POST['artist_id']) ? trim($_POST['artist_id']) : '';
    $debut      = isset($_POST['debut']) ? trim($_POST['debut']) : '';
    $type       = isset($_POST['type']) ? trim($_POST['type']) : 'album';
    
    $uploadOk = 1;

    // 2. Process Files Using Our Reusable Function
    $artist_image_path = handleImageUpload('artist_image', $target_dir);
    $cover_image_path  = handleImageUpload('cover_image', $target_dir, "banner_");

    // Check if the file handler returned any text errors
    if (strpos($artist_image_path, 'ERROR:') === 0) {
        $message .= str_replace('ERROR: ', '', $artist_image_path);
        $uploadOk = 0;
    }
    if (strpos($cover_image_path, 'ERROR:') === 0) {
        $message .= str_replace('ERROR: ', '', $cover_image_path);
        $uploadOk = 0;
    }

    // Validate that required fields aren't completely blank
    if ($uploadOk == 1 && (empty($album_name) || empty($artist_id))) {
        $message .= "Album Name and Selected Artist are required. ";
        $uploadOk = 0;
    }

    // 3. Save Clean Data into MySQL Table
    if ($uploadOk == 1) {
        try {
            $insert_query = "INSERT INTO album (album_name, artist_id, debut, artist_image, cover_image, type) 
                             VALUES (:album_name, :artist_id, :debut, :artist_image, :cover_image, :type)";
            
            $stmt = $db->prepare($insert_query);
            $stmt->execute([
                ':album_name'   => $album_name,
                ':artist_id'    => $artist_id,
                ':debut'        => $debut,
                ':artist_image' => $artist_image_path,
                ':cover_image'  => $cover_image_path,
                ':type'         => $type
            ]);

            $message = "A new album has been registered successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageType = "danger";
        }
    } else {
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Add New Album</title>
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
                Add New Album
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
                        <input type="text" name="album_name" class="form-control form-control-custom" placeholder="Enter Album Name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Artist</label>
                        <select name="artist_id" class="form-select form-select-custom" required>
                            <option value="" disabled selected>-- Select an Artist --</option>
                            <?php foreach ($artists as $row): ?>
                                <option value="<?= intval($row['id']); ?>">
                                    <?= htmlspecialchars($row['artist_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Artist Image (PNG Only)</label>
                        <input type="file" name="artist_image" class="form-control form-control-custom" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Cover Image (PNG Only)</label>
                        <input type="file" name="cover_image" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label form-label-custom">Release Debut Date</label>
                        <input type="text" name="debut" class="form-control form-control-custom" placeholder="e.g. 23rd of August 2024" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label form-label-custom">Type</label>
                    <select name="type" class="form-select form-select-custom" required>
                        <option value="album" selected>Album</option>
                        <option value="single">Single</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-submit-custom">Add New Album</button>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>