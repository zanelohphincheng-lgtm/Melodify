<!-- Sidebars -->
    <div class="sidebar d-flex flex-column py-3">
    <div class="px-4 mb-4 d-flex align-items-center">
        <img src="upload/logo1.png" alt="M" width="32" height="32" class="logo me-2">
        <span class="fs-4 fw-bold brand-text">Melodify</span>
    </div>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= ($current_page == 'browse') ? 'active' : ''; ?>">
                <i class="bi bi-house-door text-success me-2"></i> 
                Browse
                </i>
            </a>
        </li>
        <li>
            <a href="favourite.php" class="nav-link <?= ($current_page == 'favourite') ? 'active' : ''; ?>">
                <i class="bi bi-heart-fill text-danger me-2"></i>
                Your Favourite
                <br/>
                <p class="text-danger mb-0">( NOT AVAILABLE )</p>
            </a>
        </li>
        <li>
            <a href="addplaylist.php" class="nav-link <?= ($current_page == 'create-playlist') ? 'active' : ''; ?>">
                <i class="bi bi-plus-square text-info me-2"></i> 
                Create Playlist
                <br/>
                <p class="text-danger mb-0">( NOT AVAILABLE )</p>
            </a>
        </li>
        <li>
            <a href="myplaylist.php" class="nav-link <?= ($current_page == 'my-playlist') ? 'active' : ''; ?>">
                <i class="bi bi-bookmark text-warning me-2"></i> 
                My Playlist
                <br/>
                <p class="text-danger mb-0">( NOT AVAILABLE )</p>
            </a>
        </li>
    </ul>
</div>