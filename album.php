<?php
session_start();

// Database Connection
$db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");


// 1. Fetch album ID from the URL (e.g., album.php?id=1)
$album_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($album_id <= 0) {
    die("Error: No valid album ID selected.");
}

// FIXED: Added artist_image to the SELECT query so PHP can find it!
$album_query = "SELECT album.id, album.artist_id, album.album_name, album.debut, album.cover_image, album.artist_image, artists.artist_name FROM album INNER JOIN artists ON album.artist_id = artists.id WHERE album.id = :id";
$album_stmt = $db->prepare($album_query);
$album_stmt->execute([':id' => $album_id]);
$album = $album_stmt->fetch();

// If the combination doesn't exist, it means either the album ID is wrong or the artist_id link is broken
if (!$album) {
    die("Error: Album or linked Artist not found.");
}

// 3. Fetch Songs associated with this album
$songs_query = "SELECT song_name, music_file, duration FROM songs WHERE album_id = :album_id ORDER BY id ASC";
$songs_stmt = $db->prepare($songs_query);
$songs_stmt->execute([':album_id' => $album_id]);
$songs = $songs_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - <?= htmlspecialchars($album['album_name']); ?></title>
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
        .audio-player-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(11, 15, 25, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 15px 40px;
            z-index: 9999;
        }

        .player-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        .currently-playing {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 30%;
        }

        .player-mini-cover {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .player-controls {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .control-icon {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.5rem;
            transition: color 0.2s, transform 0.2s;
        }
        .control-icon:hover {
            color: #ffffff;
            transform: scale(1.1);
        }

        .play-main {
            background: #ffffff;
            color: #0b0f19;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .play-main:hover {
            background: #3b82f6;
            color: #ffffff;
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

        <div class="tracklist-card">
            <div class="card-inner-title"><?= htmlspecialchars($album['album_name']); ?></div>
            
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
                        <?php $index = 1; ?>
                        <?php foreach ($songs as $song): ?>
                            <tr class="track-row" 
                                data-songname="<?= htmlspecialchars($song['song_name']); ?>" 
                                data-audiofile="<?= htmlspecialchars($song['music_file']); ?>">
                                <td class="text-muted-custom ps-2"><?= $index++; ?></td>
                                <td><?= htmlspecialchars($song['song_name']); ?></td>
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
        <div class="audio-player-bar">
            <div class="player-content">
                <div class="currently-playing">
                    <img src="<?= htmlspecialchars($album['cover_image']); ?>" alt="Now Playing" class="player-mini-cover">
                    <div>
                        <h5 id="player-song-title" class="m-0 text-white">Select a track</h5>
                        <small class="text-white"><?= htmlspecialchars($album['artist_name']); ?></small>
                    </div>
                </div>

                <div class="player-controls">
                    <button id="btn-prev" class="control-icon"><i class="bi bi-skip-start-fill"></i></button>
                    <button id="btn-play-toggle" class="control-icon play-main"><i class="bi bi-play-fill" id="play-icon"></i></button>
                    <button id="btn-next" class="control-icon"><i class="bi bi-skip-end-fill"></i></button>
                </div>

                <div class="player-extra" style="width: 30%;"></div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript : Core Audio Playback Engine (the track list and bottom player bar) -->
    <script>
        // Initialize the global audio engine variable
        let currentAudio = null; //By default there's no song playing
        let isPlaying = false;   //which means nothing is playing

        // Select the UI interface elements
        const playToggleBtn = document.getElementById('btn-play-toggle');     //The PLAY/PAUSE button
        const playIcon = document.getElementById('play-icon');                //The graphic icon look(Allowing the changes between play and pause icon)
        const playerSongTitle = document.getElementById('player-song-title'); //Text block that displays the Song Title

        // Add event listeners to all row selections
        document.querySelectorAll('.track-row').forEach(row => {
            row.addEventListener('click', function() {
                const songName = this.getAttribute('data-songname');   //Select the Name based on which id I've pressed
                const audioFile = this.getAttribute('data-audiofile'); //Select the AudioFile based on the id too

                // Safety fallback check if database record is missing a path
                if (!audioFile) {
                    alert("No audio file found for this track track!"); //No AudioFile, no vibe
                    return;
                }
                
                // Playing another song while playing one?
                // If a track is already playing, stop it completely first
                if (currentAudio) {
                    currentAudio.pause();
                }
                // Proceed to find the newly clicked song's data
                currentAudio = new Audio(audioFile);
                
                // Update the display text showing the newly clicked song
                playerSongTitle.innerText = songName;
                
                // Play the track file resource
                currentAudio.play();
                isPlaying = true;
                
                // Update icons toggles to Bootstrap Pause icon symbol
                playIcon.className = "bi bi-pause-fill";
            });
        });

        // Main Control Bar Center Toggle Button Interaction Rule (Allowing the REAL pause and play function)
        playToggleBtn.addEventListener('click', function() {
            if (!currentAudio) return; // Do nothing if no song has been picked yet (No selection, no music file playing)

            if (isPlaying) {
                currentAudio.pause();
                playIcon.className = "bi bi-play-fill";
                isPlaying = false;
            } else {
                currentAudio.play();
                playIcon.className = "bi bi-pause-fill";
                isPlaying = true;
            }
        });
    </script>
</body>
</html>