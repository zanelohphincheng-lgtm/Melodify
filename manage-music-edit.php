<?php
require("header.php"); // Ensure your PDO connection variable ($db) is defined here

$message = "";
$messageType = "info"; // Used to toggle Bootstrap alert styles dynamically

// 1. READ PHASE: Get song data to populate the form fields automatically
if (isset($_GET['id'])) {
    $song_id = intval($_GET['id']);
    
    $query = "SELECT * FROM songs WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $song_id]);
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If song doesn't exist, kick back to management table
    if (!$song) {
        header("Location: manage-music.php");
        exit();
    }
} else {
    header("Location: manage-music.php");
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

// Fetch ALL available albums so we can build the select dropdown correctly
try {
    $album_list_query = "SELECT id, album_name FROM album ORDER BY album_name ASC";
    $album_list_stmt = $db->query($album_list_query);
    $albums = $album_list_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $albums = [];
}

// 2. UPDATE PHASE: Handle form submission values
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "upload/audio";

    // Create the upload folder automatically if it doesn't exist yet
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Gather and trim all textual inputs from the form
    $song_name    = isset($_POST['song_name']) ? trim($_POST['song_name']) : '';
    $artist_id    = isset($_POST['artist_id']) ? trim($_POST['artist_id']) : '';
    $album_id     = isset($_POST['album_id'])  ? trim($_POST['album_id'])  : '';
    $duration     = isset($_POST['duration'])  ? trim($_POST['duration'])  : '';

    // FIX 3: Point the fallback back to the correct database container array ($album, not $artist)
    $music_file_path = $song['music_file'];
    $uploadOk = 1;

    // Validate and process album Profile Picture Upload (Only if user selected a file)
    if (isset($_FILES['music_file']) && $_FILES['music_file']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES["music_file"]["name"], PATHINFO_EXTENSION));
        if ($ext === "png") {
            $music_file_name = time() . "_" . basename($_FILES["music_file"]["name"]); 
            $music_file_path = $target_dir . $music_file_name;
            
            if (!move_uploaded_file($_FILES["music_file"]["tmp_name"], $music_file_path)) {
                $message .= "Failed to move Music File onto server. ";
                $uploadOk = 0;
            }
        } else {
            $message .= "Sorry, only MP3 files are allowed for Music_file. ";
            $uploadOk = 0;
        }
    }

    // FIX 2: Removed the broken $_POST image checks out of this empty field filter check completely!
    if ($uploadOk == 1 && !empty($song_name) && !empty($artist_id) && !empty($album_id) && !empty($duration)) {
        try {
            $update_query = "UPDATE songs SET
                             song_name = :song_name,
                             artist_id = :artist_id,
                             album_id = :album_id,
                             music_file = :music_file,
                             duration = :duration
                             WHERE id = :id";
            
            $update_stmt = $db->prepare($update_query);
            $success = $update_stmt->execute([
                ':song_name'  => $song_name,
                ':artist_id'  => $artist_id,
                ':album_id'   => $album_id,
                ':music_file' => $music_file_path,
                ':duration'   => $duration,
                ':id'         => $song_id
            ]);

            if ($success) {
                echo "<script>
                    alert('Song updated successfully!');
                    window.location.href = 'manage-music.php';
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
    <title>Melodify - Update Song</title>
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
                Update Song
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
                        <input type="text" name="song_name" class="form-control form-control-custom" value="<?= htmlspecialchars($song['song_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Artist</label>
                        <select name="artist_id" class="form-select form-select-custom" required>
                            <option value="" disabled selected>-- Select an Artist --</option>
                            <?php foreach ($artists as $row): ?>
                                <option value="<?= intval($row['id']); ?>" <?= ($row['id'] == $song['artist_id']) ? 'selected' : ''; ?>>
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
                                <option value="<?= intval($row['id']); ?>" <?= ($row['id'] == $song['album_id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($row['album_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Duration</label>
                        <input type="text" name="duration" class="form-control form-control-custom" value="<?= htmlspecialchars($song['duration']); ?>" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12 mb-6 mb-md-0">
                        <label class="form-label form-label-custom">Music File (MP3 Only)</label>
                        <input type="file" name="music_file" class="form-control form-control-custom">
                        <small class="text-info d-block mt-1">Current: <?= basename($song['music_file']); ?></small>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit-custom">Update Song</button>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>