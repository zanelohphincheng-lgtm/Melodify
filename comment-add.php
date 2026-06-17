<?php
session_start();

// 🚫 HARSH GATE: If the user is NOT logged in, kick them out immediately!
if (!isset($_SESSION['user'])) {
    header("Location: login-form.php"); // Or whatever your login filename is
    exit(); // Always stop execution after a redirect
}

$user_id  = $_SESSION['user']['id'];

$success_msg = "";
$error_msg = "";

// Handle the Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_content = trim($_POST['comment_content']);
    $album_id = isset($_POST['album_id']) ? intval($_POST['album_id']) : 0;

    if (!empty($comment_content) && $album_id > 0) {
        try {
            // Reconnect using clean PDO
            $db = new PDO("mysql:host=localhost;dbname=project_sem1", "root");

            // Insert into your database linking directly via foreign key
            $query = "INSERT INTO comments (user_id, album_id, comment_content) VALUES (:user_id, :album_id, :comment_content)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':album_id' => $album_id,
                ':comment_content' => $comment_content
            ]);
            $success_msg = "Comment added successfully!";
            header("Location: comment.php?id=" . $album_id);
            exit();
        } catch (PDOException $e) {
            die("Database error occurred: " . $e->getMessage());
        }
    } else {
        $error_msg = "Unable to add your comment! Try again later...";
        header("Location: comment.php?id=" . $album_id);
        exit();
    }
}
?>