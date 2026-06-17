<!-- Top Bar -->
<header class="d-flex justify-content-between align-items-center mb-4">
    <div class="position-relative w-25">
        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
        <form action="search.php" method="GET">
            <input type="search" name="query" class="form-control search-bar" placeholder="Search...">
        </form>
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