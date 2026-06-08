<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Sign Up</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #0b0f19;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* The Centered Glass Card Container */
        .signup-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 
                        0 0 50px rgba(139, 92, 246, 0.05);
        }

        /* Group wrapper to position icons inside inputs */
        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-custom i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a5b5;
            font-size: 1.1rem;
            z-index: 10;
        }

        .form-control-custom {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #fff;
            padding: 14px 16px 14px 48px; /* Extra left padding to clear the icon */
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        .form-control-custom::placeholder {
            color: #a0a5b5;
            opacity: 0.8;
        }

        .form-control-custom:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: none;
        }

        /* Gradient Registration Button */
        .btn-signup {
            background: linear-gradient(90deg, #2563eb, #d946ef);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.4);
            color: white;
        }

        .btn-signup:active {
            transform: translateY(0);
        }

        /* Red Go Back Button */
        .btn-back {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s, transform 0.2s;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #dc2626;
            color: white;
            transform: translateY(-1px);
        }

        .brand-title {
            font-family: 'Arial Rounded MT Bold', 'Comic Sans MS', sans-serif;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="signup-card text-center">
        <!-- Logo and App Name Header -->
        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
            <img src="upload/logo3.png" alt="Melodify Logo" width="42" height="42" class="logo">
            <h1 class="m-0 brand-title">Melodify</h1>
        </div>

        <h3 class="fw-bold mb-1">New Here?</h3>
        <p class="text-light opacity-75 small mb-4">Sign up to discover your vibe!</p>

        <!-- Signup Form Actions -->
        <form action="registration.php" method="POST">
            
            <!-- Username Input Field -->
            <div class="input-group-custom">
                <i class="bi bi-person-circle"></i>
                <input type="text" name="username" class="form-control form-control-custom" placeholder="Username" required autocomplete="username">
            </div>

            <!-- Email Input Field -->
            <div class="input-group-custom">
                <i class="bi bi-envelope"></i>
                <input type="email" name="email" class="form-control form-control-custom" placeholder="Email Address" required autocomplete="email">
            </div>
            
            <!-- Password Input Field -->
            <div class="input-group-custom">
                <i class="bi bi-lock"></i>
                <input type="password" name="password" class="form-control form-control-custom" placeholder="Password" required autocomplete="new-password">
            </div>

            <!-- Confirm Password Input Field -->
            <div class="input-group-custom">
                <i class="bi bi-shield-check"></i>
                <input type="password" name="confirm_password" class="form-control form-control-custom" placeholder="Confirm Password" required autocomplete="new-password">
            </div>

            <!-- Main Submit Action -->
            <button type="submit" class="btn btn-signup w-100 mb-4">Sign Up</button>
        </form>

        <!-- Redirect back to Login Context -->
        <p class="small text-light opacity-50 mb-4">
            Have an account? <a href="login-form.php" class="text-decoration-underline text-info fw-medium">Login</a> here
        </p>

        <!-- Go Back Button navigation context -->
        <div>
            <a href="index.php" class="btn btn-back">
                <i class="bi bi-arrow-left-circle"></i> Go Back
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>