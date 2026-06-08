<?php
session_start();

// Database Connection
try {
    $db = new PDO("mysql:host=localhost;dbname=project_sem1", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// 1. Fetch the target album based on URL parameter ID
$album_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($album_id <= 0) {
    die("Error: No valid album ID selected.");
}

// 2. Fetch Master Album Data
$album_query = "SELECT * FROM album WHERE id = :id";
$album_stmt = $db->prepare($album_query);
$album_stmt->execute([':id' => $album_id]);
$album = $album_stmt->fetch(PDO::FETCH_ASSOC);

if (!$album) {
    die("Error: Album not found in the database catalog.");
}

// 3. Fetch Child Tracks associated with this specific album
$songs_query = "SELECT * FROM songs WHERE album_id = :album_id ORDER BY id ASC";
$songs_stmt = $db->prepare($songs_query);
$songs_stmt->execute([':album_id' => $album_id]);
$songs = $songs_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - <?= htmlspecialchars($album['title']); ?></title>
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

        /* Go Back Pill Component Button */
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

        /* Album Master Header Layout Area */
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
            width: 26px;
            height: 26px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Glassmorphic Tracklist Container Table Box */
        .tracklist-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .card-inner-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 25px;
        }

        /* Audio Track Table Presentation Design styling definitions */
        .track-table {
            width: 100%;
            border-collapse: collapse;
        }

        .track-table th {
            color: #94a3b8;
            font-weight: 600;
            font-size: 1rem;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .track-table td {
            padding: 16px 0;
            font-size: 1.1rem;
            font-weight: 600;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        .track-row {
            transition: background-color 0.2s ease;
        }
        .track-row:hover {
            background-color: rgba(255, 255, 255, 0.03);
            cursor: pointer;
        }

        .text-muted-custom {
            color: #94a3b8 !important;
            font-weight: 500;
        }

        /* Responsive Breakpoint Rules for Mobile Views */
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
            <img src="upload/<?= htmlspecialchars($album['cover_image']); ?>" alt="Album Cover" class="album-cover">
            
            <div>
                <div class="brand-pill-mini">
                    <img src="upload/logo3.png" alt="Melodify Logo" width="24">
                    <span>Melodify</span>
                </div>
                <h1 class="album-display-title"><?= htmlspecialchars($album['title']); ?></h1>
                
                <div class="album-meta-details">
                    <span class="text-muted-custom">Debut :</span>
                    <span><?= htmlspecialchars($album['debut_date']); ?></span>
                    <span class="mx-1">•</span>
                    <img src="upload/<?= htmlspecialchars($album['cover_image']); ?>" class="artist-avatar-mini" alt="Artist Profile">
                    <span class="fw-bold text-info"><?= htmlspecialchars($album['artist']); ?></span>
                </div>
            </div>
        </div>

        <div class="tracklist-card">
            <div class="card-inner-title"><?= htmlspecialchars($album['title']); ?></div>
            
            <?php if (count($songs) > 0): ?>
                <table class="track-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">#</th>
                            <th style="width: 72%;">Title</th>
                            <th style="width: 20%;" class="text-end">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($songs as $song): ?>
                            <tr class="track-row">
                                <td class="text-muted-custom ps-2"><?= htmlspecialchars($song['track_number']); ?></td>
                                <td><?= htmlspecialchars($song['title']); ?></td>
                                <td class="text-end pe-2 text-muted-custom"><?= htmlspecialchars($song['duration']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-4 text-muted-custom">
                    <i class="bi bi-music-note-list fs-2 d-block mb-2"></i>
                    No songs have been cataloged inside this album yet.
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>