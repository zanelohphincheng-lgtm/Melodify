<?php
session_start();
$db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");
$current_page = 'favourite';

// 🚫 Gate: Only logged-in members can have favorites
if (!isset($_SESSION['user'])) {
    header("Location: login-form.php");
    exit();
}

// $user_id = $_SESSION['user']['id'];
// $favorites = [];

//     // SQL JOIN: Pull favorite records linked to user account
//     $query = "SELECT album.id AS album_id, album.album_name, album.cover_image, artists.artist_name 
//               FROM user_favorites 
//               INNER JOIN album ON user_favorites.song_id = album.id 
//               INNER JOIN artists ON album.artist_id = artists.id
//               WHERE user_favorites.user_id = :user_id 
//               ORDER BY user_favorites.created_at DESC";
              
//     $stmt = $db->prepare($query);
//     $stmt->execute([':user_id' => $user_id]);
//     $favorites = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Your Favourite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        * {
            color: white;
        }
        :root {
            --bg-dark: #0b0f19;
            --sidebar-bg: #111625;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body {
            background-color: var(--bg-dark);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Identical Sidebar Navigation Layout */
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

        /* Identical Main Content Frame Layout */
        .main-content {
            margin-left: 240px;
            padding: 20px 40px;
            min-height: 100vh;
        }

        /* Identical Top Search Bar Layout */
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
        }
        
        .bi-search {
            color: white !important;
        }

        /* Matching Grid Style Cards */
        .media-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .media-card.glow-red:hover { 
            border-color: #ef4444; 
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.3); 
        }

        .media-card:hover {
            transform: translateY(-5px);
        }

        .album-art {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 8px;
            object-fit: cover;
        }

        .logo {
            border-radius: 5px;
        }

        #userMenu {
            border-radius: 20px; 
            background: rgba(255,255,255,0.02); 
            border-color: rgba(255,255,255,0.3);
            color: white;
        }

        #userMenu:hover {
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.4);
            color: white;
        }

        .dashboard-text {
            color: rgb(0, 115, 255);
        }

        .dashboard-text:hover {
            color: rgb(0, 115, 255);
        }

        .brand-text {
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Arial Rounded MT Bold', 'Comic Sans MS', sans-serif;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php require('sidebar.php') ?>

    <!-- Favourite Main Content -->
    <div class="main-content">
        <?php require('topbar.php')?>

        <section class="mb-5">
            <h2 class="display-5 fw-bold mb-4">Your Favourite</h2>
            
            <!-- Data -->
            <?php if (!empty($favorites)): ?>
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-4">
                    <?php foreach ($favorites as $albumItem): ?>
                        <div class="col">
                            <div class="card media-card h-100 p-3 glow-red" onclick="window.location.href='album.php?id=<?= $albumItem['album_id'] ?>'">
                                <img src="<?= htmlspecialchars($albumItem['cover_image']) ?>" class="album-art mb-3" alt="Album Cover">
                                <p class="m-0 fw-medium text-truncate small"><?= htmlspecialchars($albumItem['album_name']) ?></p>
                                <p class="m-0 fw-medium text-truncate small text-muted"><?= htmlspecialchars($albumItem['artist_name']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5" style="background: var(--glass-bg); border: 1px dashed var(--glass-border); border-radius: 16px;">
                    <i class="bi bi-heartbreak display-3 mb-3 text-secondary d-block"></i>
                    <p class="fs-5 m-0 text-secondary">No favorite song yet...</p>
                    <small class="text-info d-block mt-1">AVAILABLE ON NEXT UPDATE</small>
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