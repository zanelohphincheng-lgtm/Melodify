<?php
session_start();
if(isset($_GET['id'])){
    $id=$_GET['id'];
    $db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");

    // Load Artist data
    $artist_query = "SELECT * FROM artists WHERE id=:id";
    $stmt = $db->prepare($artist_query);
    $stmt->execute([':id'=>$id]);
    $artist = $stmt->fetch();

    // Load album data
    $album_query = "SELECT id, album_name, cover_image, type FROM album WHERE artist_id = :artist_id";
    $stmt = $db->prepare($album_query);
    $stmt->execute([':artist_id' => $id]);
    $albums_list = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Artist Biography</title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --bg-base: #070a13;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.07);
            --accent-blue: #2563eb;
            --accent-glow: rgba(37, 99, 235, 0.4);
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-base);
            color: #ffffff;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient background glow effects */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, rgba(0,0,0,0) 70%);
            top: -100px;
            right: -50px;
            z-index: -1;
        }

        .main-wrapper {
            max-width: 1140px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Navigation Button */
        .btn-back-nav {
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: 600;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back-nav:hover {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            box-shadow: 0 4px 20px var(--accent-glow);
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* Hero Banner Section */
        .artist-hero-card {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            margin-top: 30px;
            margin-bottom: 40px;
            height: 380px;
            display: flex;
            align-items: flex-end;
            background: linear-gradient(to top, rgba(7, 10, 19, 0.95) 10%, rgba(7, 10, 19, 0.4) 50%, rgba(0,0,0,0) 100%);
            border: 1px solid var(--glass-border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        /* Simulated background image placement */
        .artist-hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
            filter: brightness(0.85);
        }

        .artist-hero-content {
            padding: 40px;
            width: 100%;
            position: relative;
        }

        .verified-badge {
            background: var(--accent-blue);
            color: #white;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 12px;
            box-shadow: 0 0 15px var(--accent-glow);
        }

        .artist-main-name {
            font-size: 4rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
            letter-spacing: -1px;
        }

        .listener-count {
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 8px;
            font-size: 1.1rem;
        }

        /* Split content structure */
        .biography-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .panel-heading {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
        }

        .panel-heading i {
            color: var(--accent-blue);
        }

        /* Biography Details */
        .bio-text p {
            color: #cbd5e1;
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .artist-stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .stat-box {
            text-align: center;
        }

        .stat-val {
            font-size: 1.6rem;
            font-weight: 700;
            color: #ffffff;
            display: block;
        }

        .stat-lbl {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Top Tracks List Configuration */
        .track-mini-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .track-mini-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-radius: 14px;
            background: rgba(255,255,255, 0.01);
            border: 1px solid transparent;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }

        .track-mini-item:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: var(--glass-border);
            color: #ffffff;
            transform: translateX(4px);
        }

        .track-left-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .track-rank {
            font-weight: 700;
            color: var(--text-muted);
            width: 20px;
        }

        .track-thumb {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
        }

        .track-details h6 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
        }

        .track-details small {
            color: var(--text-muted);
        }

        .track-duration {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        
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

        @media (max-width: 992px) {
            .biography-grid {
                grid-template-columns: 1fr;
            }
            .artist-main-name {
                font-size: 3rem;
            }
            .artist-hero-card {
                height: 320px;
            }
        }
    </style>
</head>
<body>

    <div class="main-wrapper">
        <!-- Structural Header Controls -->
        <div>
            <a href="index.php"class="btn-pill-custom btn-blue-pill">
                <i class="bi bi-arrow-left"></i> Go Back
            </a>
        </div>

        <!-- Artist Feature Spotlight Deck -->
            <div class="artist-hero-card">
                <!-- Dynamic placeholder background template file representing structural image logic -->
                <img src="<?= $artist['artist_banner'] ?>" alt="Artist Spotlight" class="artist-hero-bg">
                
                <div class="artist-hero-content">
                    <div class="verified-badge">
                        <i class="bi bi-patch-check-fill"></i> Verified Artist
                    </div>
                    <h1 class="artist-main-name"><?= $artist['artist_name'] ?></h1>
                    <div class="listener-count">
                        <i class="bi bi-headset"></i> <?= $artist['monthly_listener'] ?>
                    </div>
                </div>
            </div>

            <!-- Responsive Split Presentation Module -->
            <div class="biography-grid">
                
                <!-- Left Panel: Profile Journey Narrative Description -->
                <div class="glass-panel">
                    <div class="panel-heading">
                        <i class="bi bi-file-person-fill"></i> Inside the Melody
                    </div>
                    <div class="bio-text">
                        <p><?= $artist['artist_biography'] ?></p>
                    </div>

                    <!-- Structured Fact Matrix Metrics Container -->
                    <div class="artist-stats-row">
                        <div class="stat-box">
                            <span class="stat-val">6</span>
                            <span class="stat-lbl">Albums</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-val">12B+</span>
                            <span class="stat-lbl">Streams</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-val">Penang</span>
                            <span class="stat-lbl">Tour Base</span>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Popular Releases Index List Module -->
                <div class="glass-panel">
                    <div class="panel-heading">
                        <i class="bi bi-fire"></i> Popular Releases
                    </div>
                    
                    <div class="track-mini-list">
                        <?php if (!empty($albums_list)): ?>
                            <?php foreach($albums_list as $index => $single_album): ?>
                                
                                <a href="album.php?id=<?= $single_album['id'] ?>" class="track-mini-item">
                                    <div class="track-left-meta">
                                        <span class="track-rank"><?= $index + 1 ?></span>
                                        
                                        <img src="<?= htmlspecialchars($single_album['cover_image']) ?>" alt="Track Cover" class="track-thumb">
                                        
                                        <div class="track-details">
                                            <h6><?= htmlspecialchars($single_album['album_name']) ?></h6>
                                            <h6 class="text-capitalize"><?= htmlspecialchars($single_album['type']) ?></h6>
                                        </div>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted small"></i>
                                </a>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted m-0 small">No releases found for this artist.</p>
                        <?php endif; ?>
                    </div>
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>