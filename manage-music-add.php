<?php
require("header.php"); // Ensure your PDO connection variable ($db) is defined here

$message = "";
$messageType = "info"; // Used to toggle Bootstrap alert styles (e.g., success, danger)

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

// FETCH ALL ALBUM: Populate the relational selector dropdown element dynamically
try {
    $album_query = "SELECT id, album_name FROM album ORDER BY album_name ASC";
    $album_stmt = $db->query($album_query);
    $albums = $album_stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error pulling albums configuration data: " . $e->getMessage();
    $messageType = "danger";
    $albums = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Process MP3 File for music_file
    $target_dir = "upload/audio/";
    
    // Create the upload folder automatically if it doesn't exist yet
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $music_file_path = "";
    $uploadOk = 1;

    // Validate and process MP3 File Upload
    if (isset($_FILES['music_file']) && $_FILES['music_file']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES["music_file"]["name"], PATHINFO_EXTENSION));
        
        if ($ext === "mp3") {
            // Generate unique filename using timestamp to prevent file overwrites
            $music_file_name = time() . "_" . basename($_FILES["music_file"]["name"]); 
            $music_file_path = $target_dir . $music_file_name;
            
            if (!move_uploaded_file($_FILES["music_file"]["tmp_name"], $music_file_path)) {
                $message = "Failed to move uploaded audio file onto server. ";
                $uploadOk = 0;
            }
        } else {
            $message = "Sorry, only MP3 files are allowed for the music track. ";
            $uploadOk = 0;
        }
    } else {
        $message = "Please select a valid music file to upload. ";
        $uploadOk = 0;
    }

    // 2. Save paths and text form payloads safely into MySQL via PDO Prepared Statements
    if ($uploadOk == 1) {
        try {
            // FIX: Cleansed tokens to align exactly with structural table names and properties
            $insert_query = "INSERT INTO songs (song_name, artist_id, album_id, duration, music_file) 
                             VALUES (:song_name, :artist_id, :album_id, :duration, :music_file)";
            
            $stmt = $db->prepare($insert_query);
            
            // FIX: Ensured exactly 6 parameters map cleanly into the 6 SQL tokens above
            $stmt->execute([
                ':song_name'  => $_POST['song_name'],
                ':artist_id'  => $_POST['artist_id'],
                ':album_id'   => $_POST['album_id'],
                ':duration'   => $_POST['duration'],
                ':music_file' => $music_file_path
            ]);

            $message = "A new song has been registered successfully!";
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
    <title>Melodify - Add New Song</title>
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
            <a href="manage-music.php" class="btn-pill-back">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
            <div class="panel-title text-center flex-grow-1 me-5">
                <img src="upload/logo3.png" alt="Logo" width="32" class="me-2 mb-1">
                Add New Song
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
                        <label class="form-label form-label-custom">Song Name</label>
                        <input type="text" name="song_name" class="form-control form-control-custom" placeholder="Enter Song Name" required>
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
                        <label class="form-label form-label-custom">Album</label>
                        <select name="album_id" class="form-select form-select-custom" required>
                            <option value="" disabled selected>-- Select an Album --</option>
                            <?php foreach ($albums as $row): ?>
                                <option value="<?= intval($row['id']); ?>">
                                    <?= htmlspecialchars($row['album_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Duration</label>
                        <input type="text" name="duration" class="form-control form-control-custom" placeholder="Enter Song Duration" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12 mb-6 mb-md-0">
                        <label class="form-label form-label-custom">Music File (MP3 Only)</label>
                        <input type="file" name="music_file" class="form-control form-control-custom" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit-custom">Add New Song</button>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>