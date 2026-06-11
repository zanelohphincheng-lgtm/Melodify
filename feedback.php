<?php
session_start();

// 🚫 HARSH GATE: If the user is NOT logged in, kick them out immediately!
if (!isset($_SESSION['user'])) {
    header("Location: login-form.php"); // Or whatever your login filename is
    exit(); // Always stop execution after a redirect
}

$user_id  = $_SESSION['user']['id'];
$username = $_SESSION['user']['username'];
$email    = $_SESSION['user']['email'];

$success_msg = "";
$error_msg = "";

// Handle the Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $feedback_content = trim($_POST['feedback_content']);

    if (!empty($feedback_content)) {
        try {
            // Reconnect using clean PDO
            $db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insert into your database linking directly via foreign key
            $query = "INSERT INTO feedback (user_id, feedback_content) VALUES (:user_id, :feedback_content)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':feedback_content' => $feedback_content
            ]);

            $success_msg = "Thank you for sharing! Your feedback has been submitted successfully.";
        } catch (PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    } else {
        $error_msg = "You can't submit an empty feedback box!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Base Setup matching Melodify Aesthetic */
        body {
            background-color: #070a13;
            color: #ffffff;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header-nav {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .btn-go-back {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s, background-color 0.2s;
        }

        .btn-go-back:hover {
            background-color: #1d4ed8;
            color: white;
            transform: translateY(-2px);
        }

        .brand-logo-container {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-text {
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Arial Rounded MT Bold', 'Comic Sans MS', sans-serif; /* Fallbacks for a playful rounded font */
            font-weight: bold;
            margin: 0;
        }

        /* Glassmorphic Form Card panel Container */
        .feedback-wrapper {
            max-width: 700px;
            width: 90%;
            margin: 40px auto;
        }

        .feedback-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .form-label-custom {
            font-size: 1.1rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        /* Clean White Input Elements */
        .form-control-custom {
            background-color: #ffffff !important;
            color: #000000 !important;
            border: none;
            border-radius: 12px;
            padding: 14px 20px;
            font-size: 1rem;
            font-weight: 500;
            width: 100%;
            outline: none;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .form-control-custom::placeholder {
            color: #a1a1aa;
            font-weight: 400;
        }

        /* Vibrant Gradient Submit Button */
        .btn-submit-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #d946ef 100%);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(217, 70, 239, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-submit-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(217, 70, 239, 0.5);
        }

        .btn-submit-gradient:active {
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <a href="index.php" class="btn-go-back">
            <i class="bi bi-arrow-left-circle-fill"></i> Go Back
        </a>
        <div class="brand-logo-container">
            <img src="upload/logo3.png" alt="Melodify Logo" width="42" height="42">
            <h1 class="brand-text">Melodify</h1>
        </div>
        <div style="width: 110px;"></div> 
    </header>

    <main class="feedback-wrapper">
        <div class="feedback-card">
            <h2 class="text-center page-title mb-4">Feedback</h2>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success bg-success text-white border-0"><?= $success_msg ?></div>
            <?php endif; ?>
            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger bg-danger text-white border-0"><?= $error_msg ?></div>
            <?php endif; ?>

            <form action="Feedback.php" method="POST">
                
                <div class="mb-4">
                    <label class="form-label-custom">Name :</label>
                    <input type="text" name="username" class="form-control-custom" placeholder="Enter your name" value="<?= htmlspecialchars($username) ?>" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Email :</label>
                    <input type="email" name="email" class="form-control-custom" placeholder="Enter your email" value="<?= htmlspecialchars($email) ?>" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Feedback :</label>
                    <textarea name="feedback_content" class="form-control-custom" rows="6" placeholder="Write your feedback here..." required></textarea>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" class="btn-submit-gradient">
                        Submit Feedback
                    </button>
                </div>

            </form>
        </div>
    </main>

    <footer></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>