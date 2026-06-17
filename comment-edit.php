<?php
session_start();

// Database Connection
$db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");

// 🚫 HARSH GATE: If the user is NOT logged in, kick them out immediately!
if (!isset($_SESSION['user'])) {
    header("Location: login-form.php"); // Or whatever your login filename is
    exit(); // Always stop execution after a redirect
}

$user_id  = $_SESSION['user']['id'];

// 1. Fetch album ID from the URL (e.g., album.php?id=1)
$comment_id = 0;
if (isset($_GET['id'])) {
    $comment_id = intval($_GET['id']);
} elseif (isset($_GET['delete_id'])) {
    $comment_id = intval($_GET['delete_id']);
}

if ($comment_id <= 0) {
    die("Error: No comment selected or ID lost.");
}

$success_msg = "";
$error_msg = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_comment'])) {
    $comment_content = trim($_POST['comment_content']);
    
    if (!empty($comment_content)) {
        // Securely update the text block for this unique comment row
        $update_query = "UPDATE comments SET comment_content = :comment_content WHERE id = :id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->execute([
            ':comment_content' => $comment_content,
            ':id' => $comment_id
        ]);
        
        $success_msg = "Your comment has been updated successfully!";
    } else {
        $error_msg = "Comment box cannot be left completely empty.";
    }
}

// Comment data
$comment_query = "SELECT comments.id, comments.album_id, comments.comment_content, users.username
                  FROM comments 
                  INNER JOIN users ON comments.user_id = users.id
                  WHERE comments.id = :comment_id";
$stmt = $db->prepare($comment_query);
$stmt->execute([':comment_id' => $comment_id]);
$comment = $stmt->fetch(); 

if (!$comment) {
    die("Error: Comment not found in the database.");
}

// ==========================================
// ⭐️ UPDATED DELETION LOGIC BLOCK ⭐️
// ==========================================
if (isset($_GET['delete_id'])) {
    
    // 1. FIRST: Look up the album_id from the database WHILE the comment still exists!
    $lookup_stmt = $db->prepare("SELECT album_id FROM comments WHERE id = :id");
    $lookup_stmt->execute([':id' => $comment_id]);
    $comment_data = $lookup_stmt->fetch(PDO::FETCH_ASSOC); // Enforce associative array mapping
    
    if ($comment_data && !empty($comment_data['album_id'])) {
        // Save the album ID securely into our tracking variable
        $album_id = $comment_data['album_id'];
        
        // 2. SECOND: Now that we know where to go back to, safely execute the deletion
        $delete_stmt = $db->prepare("DELETE FROM comments WHERE id = :id");
        $delete_stmt->execute([':id' => $comment_id]);
        
        // 3. THIRD: Redirect back to the album using the saved ID variable
        header("Location: comment.php?id=" . $album_id);
        exit();
    } else {
        // Fallback: If for some reason the comment row was already missing, send them to the main page instead of crashing
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodify - Edit Comment</title>
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

        .btn-purple-pill {
            background: linear-gradient(90deg, #a855f7, #d946ef);
            color: white;
        }
        .btn-purple-pill:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 15px rgba(217, 70, 239, 0.3);
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

        .comment-wrapper {
            max-width: 700px;
            width: 90%;
            margin: 40px auto;
        }

        .comment-card {
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
            color: #1d1d1d !important;
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
            color: #1d1d1d;
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
        
        .btn-delete-custom {
            background: linear-gradient(135deg, #eb2525 0%, #ef4646 100%);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(239, 70, 70, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
        }

        .btn-delete-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(239, 70, 70, 0.5);
        }

        .btn-delete-custom:active {
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <header class="header-nav">
        <div class="d-flex justify-content-between align-items-center mb-4 w-100 px-1">
            <a href="album.php?id=<?= $comment ['album_id']?>" class="btn-go-back">
                <i class="bi bi-arrow-left-circle-fill"></i> Go Back
            </a>
            <div class="brand-logo-container">
                <img src="upload/logo3.png" alt="Melodify Logo" width="42" height="42">
                <h1 class="brand-text">Melodify</h1>
            </div>
        </div>
    </header>

    <main class="comment-wrapper">
        <div class="comment-card">
            <h2 class="text-center page-title mb-0">Edit Comment</h2>

            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success bg-success text-white border-0"><?= $success_msg ?></div>
            <?php endif; ?>
            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger bg-danger text-white border-0"><?= $error_msg ?></div>
            <?php endif; ?>

            <form action="comment-edit.php?id=<?= $comment_id ?>" method="POST">
                
                <div class="mb-4">
                    <label class="form-label-custom">Name :</label>
                    <input type="text" name="username" class="form-control-custom" placeholder="Username" value="<?= htmlspecialchars($comment['username']) ?>" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Comment :</label>
                    <textarea name="comment_content" class="form-control-custom" rows="6" placeholder="Your Comment"><?= htmlspecialchars($comment['comment_content']) ?></textarea>
                </div>
                <div class="row g-3 text-center mt-4">
                    <div class="col-md-12 mb-4">
                        <button type="submit" name="update_comment" class="btn-submit-gradient w-100">
                            Update Comment
                        </button>
                    </div>  
                    <div class="col-md-12">
                        <a href="comment-edit.php?delete_id=<?= $comment_id ?>" 
                           class="btn-delete-custom w-100"
                           onclick="return confirm('Are you sure you want to permanently delete this comment? This cannot be undone.');">
                            Delete Comment
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>