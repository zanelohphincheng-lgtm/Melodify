<?php
session_start();

// Database Connection
$db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");

// 🚫 HARSH GATE: If the user is NOT logged in, kick them out immediately!
if (!isset($_SESSION['user'])) {
    header("Location: login-form.php"); // Or whatever your login filename is
    exit(); // Always stop execution after a redirect
}

$user_id  = $_SESSION['user']['id'];

// 1. Fetch album ID from the URL (e.g., album.php?id=1)
$album_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($album_id <= 0) {
    die("Error: No valid album ID selected.");
}

// FIXED: Added artist_image to the SELECT query so PHP can find it!
$album_query = "SELECT album.id, album.artist_id, album.album_name, album.debut, album.cover_image, album.artist_image, artists.artist_name 
                FROM album 
                INNER JOIN artists ON album.artist_id = artists.id 
                WHERE album.id = :id";
$album_stmt = $db->prepare($album_query);
$album_stmt->execute([':id' => $album_id]);
$album = $album_stmt->fetch();

// If the combination doesn't exist, it means either the album ID is wrong or the artist_id link is broken
if (!$album) {
    die("Error: Album or linked Artist not found.");
}

// Comment data
$comment_query = "SELECT comments.id, comments.comment_content, comments.submitted_at, users.username
                  FROM comments 
                  INNER JOIN users ON comments.user_id = users.id
                  WHERE comments.album_id = :album_id
                  ORDER BY comments.submitted_at DESC";
$stmt = $db->prepare($comment_query);
$stmt->execute([':album_id' => $album_id]);
$comment = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Comment <?= htmlspecialchars($album['album_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #0b0f19;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 30px;
        }

        .view-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .btn-pill-back {
            border-radius: 20px;
            padding: 6px 18px;
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

        .album-header-deck {
            display: flex;
            align-items: flex-end;
            gap: 30px;
            margin-top: 25px;
            margin-bottom: 40px;
        }

        .album-cover {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
        }

        .brand-pill-mini {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .album-display-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0 0 10px 0;
            line-height: 1.1;
        }

        .album-meta-details {
            font-size: 1.1rem;
            color: #cbd5e1;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .artist-avatar-mini {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .card-inner-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 25px;
        }

        .text-muted-custom {
            color: #94a3b8 !important;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .album-header-deck {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 20px;
            }
            .album-meta-details {
                justify-content: center;
            }
            .album-display-title {
                font-size: 2.5rem;
            }
        }
    
        .comment-input-wrapper {
            position: relative;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .comment-input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 0;
            color: #ffffff;
            font-size: 1.05rem;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .comment-input:focus {
            border-color: var(--card-glow-blue, #2d9cdb);
        }

        .comment-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .cancel-button{
            background: rgb(35, 35, 35);
            width: 80px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        .cancel-button a{
            text-decoration: none;
            color: white;
        }
        .cancel-button:hover{
            background: rgb(35, 35, 35);
            width: 80px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .comment-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 15px;
        }
        /* Square Icon Control Buttons Grid */
        .action-box {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1rem;
            transition: transform 0.2s, opacity 0.2s;
            border: none;
        }
        .action-box:hover {
            transform: scale(1.1);
            color: #0b0f19;
        }
        .bg-box-edit { background-color: #5322c5; }     /* Green */
    </style>
</head>
<body>

    <div class="view-container">
        
        <div>
            <a href="index.php" class="btn-pill-back">
                <i class="bi bi-arrow-left-circle-fill"></i> Go To Homepage
            </a>
        </div>

        <div class="album-header-deck">
            <!-- Album Cover Output -->
            <img src="<?= htmlspecialchars($album['cover_image']); ?>" alt="Album Cover" class="album-cover">
            
            <div>
                <div class="brand-pill-mini">
                    <img src="upload/logo3.png" alt="Melodify Logo" width="24">
                    <span>Melodify</span>
                </div>
                <h1 class="album-display-title"><?= htmlspecialchars($album['album_name']); ?></h1>
                
                <div class="album-meta-details">
                    <span class="text-muted-custom">Debut :</span>
                    <span><?= htmlspecialchars($album['debut']); ?></span>
                    <span class="mx-1">•</span>
                    <!-- Fixed Avatar Image Tag -->
                    <img src="<?= htmlspecialchars($album['artist_image']); ?>" class="artist-avatar-mini" alt="Artist Profile">
                    <a href="artist.php?id=<?= $album['artist_id'] ?>" class="text-decoration-none">
                        <span class="fw-bold text-info"><?= htmlspecialchars($album['artist_name']); ?></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Comments -->
        <section class="mb-5">
            <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Comment Section : <i class="bi bi-chat-heart text-secondary"></i></h3>
            <div class="row g-3">
                <!-- Comment bar -->
                <form action="comment-add.php" method="POST">
                    <input type="hidden" name="album_id" value="<?= $album['id']; ?>">
                    <div class="comment-input-wrapper">
                        <input type="text" name="comment_content" class="comment-input" placeholder="Add a comment..." required>
                    </div>
                    <button type="submit" style="display:none;"></button> 
                </form>
                <button class="btn cancel-button"><a href="album.php?id=<?= $album ['id']?>">Cancel</a></button>
            </div>

            </br>
            
            <div class="row g-3"> 
            <?php if (empty($comment)): ?>
                <p class="text-info text-uppercase text-center">No comment for this album yet!</p>
            <?php else: ?>
                <?php foreach ($comment as $comments): ?>
                <div class="col-md-6">
                    <div class="comment-item">
                        <div class="d-flex align-items-center mb-2 justify-content-between w-100">
                            <i class="bi bi-person-circle me-2 text-info">
                                <span class="fw-medium small"><?= $comments ['username'] ?></span>
                            </i>
                            <?php if(isset($_SESSION['user']['role']) || $_SESSION['user']['role'] == "admin"):  ?>
                                <a href="comment-edit.php?id=<?= $comments['id']; ?>" class="action-box bg-box-edit" title="Edit Comment">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <p class="m-0 text-light opacity-75 small"><?= $comments ['comment_content'] ?></p>
                        <p class="m-0 text-end text-secondary opacity-75 small"><?= $comments ['submitted_at'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="px-3 text-secondary small text-center">
        &copy; 2026 Melodify copyrights
    </div>
    <br/>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>