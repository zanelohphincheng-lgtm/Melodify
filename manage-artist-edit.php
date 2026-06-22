<?php
require("header.php");

$message = "";
$messageType = "info"; // Used to toggle Bootstrap alert styles (e.g., success, danger)

// 1. READ PHASE: Get artist data to populate the form fields automatically
if (isset($_GET['id'])) {
    $artist_id = intval($_GET['id']);
    
    $query = "SELECT * FROM artists WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $artist_id]);
    $artist = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If artist doesn't exist, kick back to management table
    if (!$artist) {
        header("Location: manage-artist.php");
        exit();
    }
} else {
    header("Location: manage-artist.php");
    exit();
}

// 2. UPDATE PHASE: Handle form submission values
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "upload/";

    // Create the upload folder automatically if it doesn't exist yet
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Gather and trim all textual inputs from the form
    $artist_name       = isset($_POST['artist_name']) ? trim($_POST['artist_name']) : '';
    $artist_biography  = isset($_POST['artist_biography']) ? trim($_POST['artist_biography']) : '';
    $artist_instagram  = isset($_POST['artist_instagram']) ? trim($_POST['artist_instagram']) : '';
    $artist_twitter    = isset($_POST['artist_twitter']) ? trim($_POST['artist_twitter']) : '';
    $artist_tiktok     = isset($_POST['artist_tiktok']) ? trim($_POST['artist_tiktok']) : '';
    $artist_album      = isset($_POST['artist_album']) ? trim($_POST['artist_album']) : '';
    $artist_streams    = isset($_POST['artist_streams']) ? trim($_POST['artist_streams']) : '';
    $artist_tourbase   = isset($_POST['artist_tourbase']) ? trim($_POST['artist_tourbase']) : '';
    $monthly_listener  = isset($_POST['monthly_listener']) ? trim($_POST['monthly_listener']) : '';

    // CRITICAL FIX: Default path variables directly back to the existing database record values!
    $artist_image_path = $artist['artist_image'];
    $artist_banner_path = $artist['artist_banner'];
    $uploadOk = 1;

    // Validate and process Artist Profile Picture Upload (Only if artist selected a file)
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

    // Validate and process Wide Billboard Banner Upload (Only if artist selected a file)
    if (isset($_FILES['artist_banner']) && $_FILES['artist_banner']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES["artist_banner"]["name"], PATHINFO_EXTENSION));
        if ($ext === "png") {
            $artist_banner_name = time() . "_banner_" . basename($_FILES["artist_banner"]["name"]); 
            $artist_banner_path = $target_dir . $artist_banner_name;
            
            if (!move_uploaded_file($_FILES["artist_banner"]["tmp_name"], $artist_banner_path)) {
                $message .= "Failed to move Banner Image file onto server. ";
                $uploadOk = 0;
            }
        } else {
            $message .= "Sorry, only PNG files are allowed for Banner Image. ";
            $uploadOk = 0;
        }
    }

    // Check that required text inputs are not empty (Notice images are missing here because they use fallbacks)
    if ($uploadOk == 1 && !empty($artist_name) && !empty($artist_biography) && !empty($artist_instagram) && !empty($artist_twitter) && !empty($artist_tiktok) && !empty($artist_album) && !empty($artist_streams) && !empty($artist_tourbase) && !empty($monthly_listener)) {
        try {
            $update_query = "UPDATE artists SET 
                            artist_name = :artist_name, 
                            artist_biography = :artist_biography, 
                            artist_image = :artist_image, 
                            artist_banner = :artist_banner, 
                            artist_instagram = :artist_instagram,
                            artist_twitter = :artist_twitter,
                            artist_tiktok = :artist_tiktok,
                            artist_album = :artist_album,
                            artist_streams = :artist_streams,
                            artist_tourbase = :artist_tourbase,
                            monthly_listener = :monthly_listener
                            WHERE id = :id";
            
            $update_stmt = $db->prepare($update_query);
            $success = $update_stmt->execute([
                ':artist_name'      => $artist_name,
                ':artist_biography' => $artist_biography,
                ':artist_image'     => $artist_image_path, // Either new path or old database record path
                ':artist_banner'    => $artist_banner_path, // Either new path or old database record path
                ':artist_instagram' => $artist_instagram,
                ':artist_twitter'   => $artist_twitter,
                ':artist_tiktok'    => $artist_tiktok,
                ':artist_album'     => $artist_album,
                ':artist_streams'   => $artist_streams,
                ':artist_tourbase'  => $artist_tourbase,
                ':monthly_listener' => $monthly_listener,
                ':id'               => $artist_id
            ]);

            if ($success) {
                // If the admin edited their own active account profile, update their live session values!
                if (isset($_SESSION['artist']['id']) && $artist_id == $_SESSION['artist']['id']) {
                    $_SESSION['artist']['artist_name'] = $artist_name;
                    $_SESSION['artist']['artist_biography'] = $artist_biography;
                    $_SESSION['artist']['artist_image'] = $artist_image_path;
                    $_SESSION['artist']['artist_banner'] = $artist_banner_path;
                    $_SESSION['artist']['artist_instagram'] = $artist_instagram;
                    $_SESSION['artist']['artist_twitter'] = $artist_twitter;
                    $_SESSION['artist']['artist_tiktok'] = $artist_tiktok;
                    $_SESSION['artist']['artist_album'] = $artist_album;
                    $_SESSION['artist']['artist_streams'] = $artist_streams;
                    $_SESSION['artist']['artist_tourbase'] = $artist_tourbase;
                    $_SESSION['artist']['monthly_listener'] = $monthly_listener;
                }
                
                echo "<script>
                    alert('Artist updated successfully!');
                    window.location.href = 'manage-artist.php';
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
    <title>Melodify - Update Artist</title>
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
        .panel-title { font-size: 1.8rem; font-weight: 700; }
        .btn-pill-back {
            border-radius: 20px; padding: 8px 20px; font-weight: 600; text-decoration: none;
            background: linear-gradient(90deg, #729fff, #0953ca); color: white;
            display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;
        }
        .btn-pill-back:hover { transform: translateY(-2px); color: white; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4); }
        .form-label-custom { font-size: 1.2rem; font-weight: 600; margin-bottom: 8px; color: #ffffff; }
        .form-control-custom {
            background-color: #ffffff !important; color: #0b0f19 !important; border: none;
            border-radius: 12px; padding: 12px 16px; font-size: 1.1rem; font-weight: 500;
        }
        .form-control-custom:focus { box-shadow: 0 0 0 3px rgba(217, 70, 239, 0.4); }
        .btn-submit-custom {
            background: linear-gradient(90deg, #2563eb, #d946ef); color: white; border: none;
            padding: 14px; border-radius: 15px; font-weight: 700; font-size: 1.2rem; width: 100%; margin-top: 15px; transition: 0.2s;
        }
        .btn-submit-custom:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(217, 70, 239, 0.5); color: white; }
    </style>
</head>
<body>

    <div class="add-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4 px-1">
            <a href="manage-artist.php" class="btn-pill-back">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
            <div class="panel-title text-center flex-grow-1 me-5">
                <img src="upload/logo3.png" alt="Logo" width="32" class="me-2 mb-1">
                Update Artist : <p class="text-info"><?= htmlspecialchars($artist['artist_name']); ?></p>
            </div>
        </div>

        <div class="add-card">
            <?php if (!empty($message)): ?>
                <div class="alert bg-<?= htmlspecialchars($messageType); ?> text-white border-white text-center fs-5 mb-4">
                    <?= htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Artist Name</label>
                        <input type="text" name="artist_name" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['artist_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Biography</label>
                        <input type="text" name="artist_biography" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['artist_biography']); ?>" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Artist Image (Leave blank to keep existing)</label>
                        <input type="file" name="artist_image" class="form-control form-control-custom">
                        <small class="text-info d-block mt-1">Current: <?= basename($artist['artist_image']); ?></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Banner Image (Leave blank to keep existing)</label>
                        <input type="file" name="artist_banner" class="form-control form-control-custom">
                        <small class="text-info d-block mt-1">Current: <?= basename($artist['artist_banner']); ?></small>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Artist Instagram</label>
                        <input type="text" name="artist_instagram" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['artist_instagram']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Artist Twitter</label>
                        <input type="text" name="artist_twitter" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['artist_twitter']); ?>" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Artist TikTok</label>
                        <input type="text" name="artist_tiktok" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['artist_tiktok']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Artist Albums</label>
                        <input type="number" name="artist_album" class="form-control form-control-custom" min="1" value="<?= htmlspecialchars($artist['artist_album']); ?>" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label form-label-custom">Artist Streams</label>
                        <input type="text" name="artist_streams" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['artist_streams']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Artist Tourbase</label>
                        <input type="text" name="artist_tourbase" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['artist_tourbase']); ?>" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label form-label-custom">Monthly Listener</label>
                    <input type="text" name="monthly_listener" class="form-control form-control-custom" value="<?= htmlspecialchars($artist['monthly_listener']); ?>" required>
                </div>

                <button type="submit" class="btn btn-submit-custom">Update Artist</button>
                
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>