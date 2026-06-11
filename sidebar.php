<!-- Sidebars -->
    <div class="sidebar d-flex flex-column py-3">
    <div class="px-4 mb-4 d-flex align-items-center">
        <img src="upload/logo1.png" alt="M" width="32" height="32" class="logo me-2">
        <span class="fs-4 fw-bold brand-text">Melodify</span>
    </div>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= ($current_page == 'browse') ? 'active' : ''; ?>">
                <i class="bi bi-house-door me-2"></i> Browse
            </a>
        </li>
        <li>
            <a href="favourite.php" class="nav-link <?= ($current_page == 'favourite') ? 'active' : ''; ?>">
                <i class="bi bi-heart-fill text-danger me-2"></i> Your Favourite
            </a>
        </li>
        <li>
            <a href="manage-playlist-add.php" class="nav-link <?= ($current_page == 'create-playlist') ? 'active' : ''; ?>">
                <i class="bi bi-plus-square me-2"></i> Create Playlist
            </a>
        </li>
        <li>
            <a href="manage-playlist.php" class="nav-link <?= ($current_page == 'my-playlist') ? 'active' : ''; ?>">
                <i class="bi bi-bookmark me-2"></i> My Playlist
            </a>
        </li>
    </ul>
</div>