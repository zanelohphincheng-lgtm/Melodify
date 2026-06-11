<?php
session_start();
$db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");
$current_page = 'browse';

// Load Artist data
$artist_query = "SELECT id, artist_name, artist_image FROM artists";
$stmt = $db->prepare($artist_query);
$stmt->execute([]);
$artists = $stmt->fetchAll();

// 1. FETCH TRENDING ALBUMS ONLY
$album_query = "SELECT album.id, album.album_name, album.cover_image, artists.artist_name 
                FROM album 
                INNER JOIN artists ON album.artist_id = artists.id 
                WHERE album.type = 'album'";

$stmt_albums = $db->prepare($album_query);
$stmt_albums->execute();
$trending_albums = $stmt_albums->fetchAll();


// 2. FETCH SINGLE TRACKS ONLY
$single_query = "SELECT album.id, album.album_name, album.cover_image, artists.artist_name 
                 FROM album 
                 INNER JOIN artists ON album.artist_id = artists.id 
                 WHERE album.type = 'single'";

$stmt_singles = $db->prepare($single_query);
$stmt_singles->execute();
$single_tracks = $stmt_singles->fetchAll();

    // Feedback data
    $feedback_query = "SELECT feedback.id, feedback.feedback_content, feedback.submitted_at, users.username FROM feedback INNER JOIN users ON feedback.user_id = users.id";
    $stmt = $db->prepare($feedback_query);
    $stmt->execute([]);
    $feedback = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Explore Your Soundscape</title>
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

        /* Feedback Scroller Container */
        .feedback-item {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 15px;
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php require('sidebar.php') ?>

    <!-- Top bar -->
    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div class="position-relative w-25">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" class="form-control search-bar" placeholder="Search...">
            </div>
            <div class="auth-action-zone">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle px-4" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <?= htmlspecialchars($_SESSION['user']['username']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="userMenu" style="background: #111625; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;">
                            <?php if(isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == "admin"): ?>
                                <li><a class="dropdown-menu-item dropdown-item py-2 dashboard-text" href="dashboard.php"><i class="bi bi-card-checklist me-2 dashboard-text"></i>Dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-menu-item dropdown-item py-2" href="feedback.php"><i class="bi bi-envelope-paper-heart me-2 text-white"></i>Feedback</a></li>
                            <li><a class="dropdown-menu-item dropdown-item py-2" href="logout.php?logout=true"><i class="bi bi-box-arrow-right me-2 text-danger"></i>Log Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-2">
                        <a href="login-form.php" class="btn btn-outline-light px-4" style="border-radius: 20px;">Log In</a>
                        <a href="register-form.php" class="btn btn-light px-4" style="border-radius: 20px; font-weight: 600;">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </header>

    <!-- Homepage -->
        <section class="hero-banner mb-5 text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 fw-bold mb-3">EXPLORE YOUR SOUNDS AND VOICE.</h1>
                    <p class="lead text-light mb-4 opacity-75">Rediscover the music you love, and find your new obsessions.</p>
                    <a href="#music"><button class="btn btn-gradient btn-lg">START LISTENING NOW</button></a>
                </div>
            </div>
        </section>

        <!-- Artist showcase -->
        <section class="mb-5">
            <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Featured Artists <i class="bi bi-arrow-through-heart text-secondary"></i></h3>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-4">
            
            <!-- Render artist data -->
            <?php foreach ($artists as $artist): ?>
                <div class="col">
                    <div class="card media-card h-100 p-3 glow-pink" onclick="window.location.href='artist.php?id=<?= $artist['id'] ?>'">
                        <img src="<?= $artist['artist_image'] ?>" class="avatar-circle mb-3" alt="Artist Profile">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="m-0 fw-medium text-truncate small"><?= $artist['artist_name'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            </div>
        </section>

        <!-- Album showcase -->
        <section class="mb-5" id="music">
            <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Trending Albums <i class="bi bi-file-earmark-music text-secondary"></i></h3>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-4">
            
            <!-- Render album data -->
            <?php foreach ($trending_albums as $albumItem): ?>
                <div class="col">
                    <div class="card media-card h-100 p-3 glow-blue" onclick="window.location.href='album.php?id=<?= $albumItem['id'] ?>'">
                        <img src="<?= $albumItem['cover_image'] ?>" class="album-art mb-3" alt="Album Cover">
                        <p class="m-0 fw-medium text-truncate small"><?= $albumItem['album_name'] ?></p>
                        <p class="m-0 fw-medium text-truncate small"><?= $albumItem['artist_name'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            </div>
        </section>

        <!-- Single showcase -->
        <section class="mb-5">
            <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Single Tracks <i class="bi bi-cassette text-secondary"></i></h3>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-4">
                
            <!-- Render single data -->
            <?php foreach ($single_tracks as $singleItem): ?>
                <div class="col">
                    <div class="card media-card h-100 p-3 glow-blue" onclick="window.location.href='album.php?id=<?= $singleItem['id'] ?>'">
                        <img src="<?= $singleItem['cover_image'] ?>" class="album-art mb-3" alt="Album Cover">
                        <p class="m-0 fw-medium text-truncate small"><?= $singleItem['album_name'] ?></p>
                        <p class="m-0 fw-medium text-truncate small"><?= $singleItem['artist_name'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </section>

        <!-- Feedback -->
        <section class="mb-5">
            <h3 class="mb-4 fw-semibold text-secondary fs-5 text-uppercase tracking-wider">Featured Feedback <i class="bi bi-chat-heart text-secondary"></i></h3>
            <div class="row g-3">

                <?php foreach ($feedback as $feedbacks): ?>
                <div class="col-md-6">
                    <div class="feedback-item">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person-circle me-2 text-info"></i>
                            <span class="fw-medium small"><?= $feedbacks ['username'] ?></span>
                        </div>
                        <p class="m-0 text-light opacity-75 small"><?= $feedbacks ['feedback_content'] ?></p>
                        <p class="m-0 text-end text-light opacity-75 small"><?= $feedbacks ['submitted_at'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="px-3 text-secondary small text-center">
        &copy; 2026 Melodify copyrights
    </div>
    <br/>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>