<?php

session_start();

if(isset($_SESSION['user'])){
    header("Location: login.php");
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Melodify | Login</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    />
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
        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 
                        0 0 50px rgba(139, 92, 246, 0.05); /* Subtle purple ambient glow */
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

        /* Gradient Login Button */
        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 70, 239, 0.4);
            color: white;
        }

        .btn-login:active {
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
            font-family: 'Arial Rounded MT Bold', 'Comic Sans MS', sans-serif; /* Fallbacks for a playful rounded font */
            font-weight: bold;
        }
    </style>
  </head>
  <body>
    <div class="login-card text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
            <img src="upload/logo3.png" alt="Melodify Logo" width="42" height="42">
            <h1 class="m-0 brand-title">Melodify</h1>
        </div>

        <h3 class="fw-bold mb-1">Welcome Back!</h3>
        <p class="text-light opacity-75 small mb-4">Log right back into your own vibe!</p>

        <form method="POST" action="login.php">
            <div class="input-group-custom">
                <label for="name" class="visually-hidden">Username</label>
                <i class="bi bi-person-circle"></i>
                <input type="text" class="form-control form-control-custom" id="username" name="username" placeholder="Username" />
            </div>

            <div class="input-group-custom">
                <label for="password" class="visually-hidden">Password</label>
                <i class="bi bi-lock"></i>
                <input type="password" class="form-control form-control-custom" id="password" name="password" placeholder="Password"/>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-4">Login</button>
        </form>

        <!-- links -->
        <p class="small text-light opacity-50 mb-4">
            Don't have an account? <a href="register-form.php" class="text-decoration-underline text-info fw-medium">Sign up</a> here
        </p>
        <p class="small text-light opacity-50 mb-4">
            <a href="change-password.php" class="text-decoration-underline text-info fw-medium">Forget Password?</a>
        </p>

        <div>
            <a href="index.php" class="btn btn-back">
                <i class="bi bi-arrow-left-circle"></i> Go To Homepage
            </a>
        </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
