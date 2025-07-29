<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $cover_image = 'uploads/' . basename($_FILES['cover_image']['name']);
    $audio_file = 'uploads/' . basename($_FILES['audio_file']['name']);

    move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image);
    move_uploaded_file($_FILES['audio_file']['tmp_name'], $audio_file);

    $stmt = $conn->prepare("INSERT INTO audiobooks (title, author, category, description, cover_image, audio_file) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $author, $category, $description, $cover_image, $audio_file])) {
        echo "<script>window.location.href='library.php';</script>";
    } else {
        $error = "Upload failed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Audiobook</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #1a1a2e, #16213e); color: #fff; margin: 0; }
        header { background: #0f3460; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 28px; color: #e94560; }
        nav a { color: #e94560; margin: 0 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .container { padding: 20px; max-width: 600px; margin: auto; }
        form { background: #0f3460; padding: 20px; border-radius: 10px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border: none; border-radius: 5px; background: #1a1a2e; color: #fff; }
        button { background: #e94560; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background: #d43f52; }
        .error { color: #ff6b6b; }
    </style>
</head>
<body>
    <header>
        <h1>Upload Audiobook</h1>
        <nav>
            <a href="javascript:window.location.href='index.php'">Home</a>
            <a href="javascript:window.location.href='library.php'">Library</a>
            <a href="javascript:window.location.href='dashboard.php'">Dashboard</a>
            <a href="javascript:window.location.href='logout.php'">Logout</a>
        </nav>
    </header>
    <div class="container">
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <select name="category" required>
                <option value="Fiction">Fiction</option>
                <option value="Non-Fiction">Non-Fiction</option>
                <option value="Self-Development">Self-Development</option>
            </select>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="file" name="cover_image" accept="image/*" required>
            <input type="file" name="audio_file" accept="audio/*" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
