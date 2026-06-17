<?php
session_start();
// 1. Connect to your database (Replace with your actual connection variables)
$db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");
try {
    $pdo = new PDO("mysql:host=localhost;dbname=project_sem1", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 2. Get and sanitize the search query from the URL
$searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';

$artists = [];
$albums = [];
$songs = [];

if (!empty($searchTerm)) {
    // The % symbols mean "match anything before or after the search term"
    $likeParam = "%" . $searchTerm . "%";
    // Query for artist
    // We use placeholders (?) to prevent SQL Injection attacks
    // SELECT DISTINCT ensures the artist ONLY appear once on the artist row
    $artiststmt = $pdo->prepare("
        SELECT DISTINCT 
            artists.id, 
            artists.artist_name, 
            artists.artist_image 
        FROM artists  
        INNER JOIN album ON artists.id = album.artist_id
        INNER JOIN songs ON artists.id = songs.artist_id
        WHERE artists.artist_name LIKE ? 
        OR album.album_name LIKE ?
        OR songs.song_name LIKE ?
    ");
    $artiststmt->execute([$likeParam, $likeParam, $likeParam]);
    $artists = $artiststmt->fetchAll(PDO::FETCH_ASSOC);

    // Query for album
    $albumstmt = $pdo->prepare("
        SELECT DISTINCT album.id, album.album_name, album.cover_image, artists.artist_name 
        FROM album 
        INNER JOIN artists ON album.artist_id = artists.id  
        INNER JOIN songs ON songs.album_id = album.id  
        WHERE album_name LIKE ? 
        OR artist_name LIKE ?
        OR songs.song_name LIKE ?
    ");
    $albumstmt->execute([$likeParam, $likeParam, $likeParam]);
    $albums = $albumstmt->fetchAll(PDO::FETCH_ASSOC);

    // Query for song
    $songstmt = $pdo->prepare("
        SELECT 
            songs.id AS song_id,
            songs.song_name,
            songs.duration,
            album.id AS album_id,
            album.album_name,
            album.cover_image,
            artists.id AS artist_id,
            artists.artist_name,
            artists.artist_image
        FROM songs
        INNER JOIN album ON songs.album_id = album.id
        INNER JOIN artists ON songs.artist_id = artists.id
        WHERE songs.song_name LIKE ? 
        OR album.album_name LIKE ? 
        OR artists.artist_name LIKE ?
    ");
    $songstmt->execute([$likeParam, $likeParam, $likeParam]);
    $songs = $songstmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Searching "<?= htmlspecialchars($searchTerm) ?>"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/mixins/container.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *{
            color: white;
        }
        :root {
            --bg-dark: #0b0f19;
            --sidebar-bg: #111625;
            --card-glow-purple: #9b51e0;
            --card-glow-blue: #2d9cdb;
            --card-glow-pink: #d04af2;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body {
            background-color: var(--bg-dark);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--glass-border);
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #b3b3b3;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            border-radius: 8px;
            margin: 4px 15px;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Main Content Frame */
        .main-content {
            margin-left: 240px;
            padding: 20px 40px;
            min-height: 100vh;
        }

        /* Search Bar Style*/
        .search-bar {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: white !important;
            border-radius: 20px;
            padding-left: 40px;
        }

        .search-bar:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(255, 255, 255, 0.2);
            color: white !important;
            box-shadow: none;
        }

        .search-bar::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
            opacity: 1; /* Overrides Firefox defaults */
        }
        
        .bi-search{
            color: white !important;
        }

        /* Hero Header Banner */
        .hero-banner {
            background: linear-gradient( #294d967f, #7d29968a), url('upload/hero-banner.png') center/cover;
            border-radius: 16px;
            padding: 60px;
            position: relative;
            border: 1px solid #7b2cbf;
            box-shadow: 0 0 40px rgba(168, 85, 247, 0.3);
        }

        .btn-gradient {
            background: linear-gradient(45deg, #7b2cbf, #3a0ca3);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        .btn-gradient:hover {
            opacity: 0.9;
            color: white;
        }

        /* Visual Media Grid Cards */
        .media-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        /* Glowing Border Highlights */
        .media-card.glow-pink:hover { border-color: #d946ef; box-shadow: 0 0 15px rgba(217, 70, 239, 0.3); }
        .media-card.glow-blue:hover { border-color: #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); }
        .media-card.glow-purple:hover { border-color: #a855f7; box-shadow: 0 0 15px rgba(168, 85, 247, 0.3); }

        .media-card:hover {
            transform: translateY(-5px);
        }

        .avatar-circle {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 50%;
            object-fit: cover;
        }

        .album-art {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Logo on the sidebar */
        .logo{
            border-radius: 5px;
        }
        #userMenu{
            border-radius: 20px; 
            background: rgba(255,255,255,0.02); 
            border-color: rgba(255,255,255,0.3);
            color: white;
        }
        #userMenu:hover{
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.4);
            color: white;
        }
        .dashboard-text{
            color:  rgb(0, 115, 255);
        }
        .dashboard-text:hover{
            color:  rgb(0, 115, 255);
        }
        .brand-text {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Arial Rounded MT Bold', 'Comic Sans MS', sans-serif; /* Fallbacks for a playful rounded font */
            font-weight: bold;
            margin: 0;
        }
        section a:active{
            color: white;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php require('sidebar.php') ?>

    <!-- Top bar -->
    <div class="main-content">
    <?php require('topbar.php') ?>
    </style>

    <div class="container">
        <h2 class="mb-4">Search Results for : <span class="text-info"><?= htmlspecialchars($searchTerm) ?></span></h2>

        <!-- ARTIST -->
        <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Artists <i class="bi bi-arrow-through-heart text-secondary"></i></h3>
        <?php if (empty($artists)): ?>
            <p class="text-info text-uppercase">No artist found matching your search.</p>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-4">
                <?php foreach ($artists as $artist): ?>
                    <div class="col">
                        <div class="card media-card h-100 p-3 glow-pink" onclick="window.location.href='artist.php?id=<?= $artist['id'] ?>'">
                            <img src="<?= $artist['artist_image'] ?>" class="avatar-circle mb-3" alt="Artist Profile">
                            <p class="m-0 fw-medium text-truncate small"><?= htmlspecialchars($artist['artist_name']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        </br>

        <!-- ALBUM -->
        <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Albums & Singles <i class="bi bi-fire text-secondary"></i></h3>
        <?php if (empty($albums)): ?>
            <p class="text-info text-uppercase">No album and single found matching your search.</p>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-4">
                <?php foreach ($albums as $album): ?>
                    <div class="col">
                        <div class="card media-card h-100 p-3 glow-blue" onclick="window.location.href='album.php?id=<?= $album['id'] ?>'">
                            <img src="<?= $album['cover_image'] ?>" class="album-art mb-3" alt="Album Cover">
                            <p class="m-0 fw-medium text-truncate small"><?= htmlspecialchars($album['album_name']) ?></p>
                            <p class="m-0 text-white text-truncate small"><?= htmlspecialchars($album['artist_name']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        </br>            

        <!-- SONG RELATE TO AN ALBUM -->
        <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Songs you may be searching <i class="bi bi-cassette text-secondary"></i></h3>
        <?php if (empty($songs)): ?>
            <p class="text-info text-uppercase">No song found matching your search.</p>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-4">
                <?php foreach ($songs as $song): ?>
                    <div class="col">
                        <div class="card media-card h-100 p-3 glow-blue" onclick="window.location.href='album.php?id=<?= $song['album_id'] ?>'">
                            <img src="<?= $song['cover_image'] ?>" class="album-art mb-3" alt="Album Cover">
                            <p class="m-0 fw-medium text-truncate small"><?= htmlspecialchars($song['song_name']) ?></p>
                            <p class="m-0 text-white text-truncate small"><?= htmlspecialchars($song['artist_name']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>