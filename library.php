<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$stmt = $conn->query("SELECT * FROM audiobooks");
$audiobooks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audiobook Library</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #1a1a2e, #16213e); color: #fff; margin: 0; }
        header { background: #0f3460; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 28px; color: #e94560; }
        nav a { color: #e94560; margin: 0 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .container { padding: 20px; max-width: 1200px; margin: auto; }
        .audiobook-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .audiobook { background: #0f3460; padding: 15px; border-radius: 10px; text-align: center; }
        .audiobook img { max-width: 100%; border-radius: 5px; }
        .audiobook h3 { font-size: 18px; margin: 10px 0; }
        .audiobook button { background: #e94560; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .audiobook button:hover { background: #d43f52; }
        @media (max-width: 600px) { .audiobook-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <h1>Audiobook Library</h1>
        <nav>
            <a href="javascript:window.location.href='index.php'">Home</a>
            <a href="javascript:window.location.href='library.php'">Library</a>
            <a href="javascript:window.location.href='dashboard.php'">Dashboard</a>
            <a href="javascript:window.location.href='upload.php'">Upload</a>
            <a href="javascript:window.location.href='logout.php'">Logout</a>
        </nav>
    </header>
    <div class="container">
        <h2>Browse Audiobooks</h2>
        <div class="audiobook-grid">
            <?php foreach ($audiobooks as $book): ?>
                <div class="audiobook">
                    <img src="<?php echo $book['cover_image']; ?>" alt="<?php echo $book['title']; ?>">
                    <h3><?php echo $book['title']; ?></h3>
                    <p><?php echo $book['author']; ?></p>
                    <button onclick="window.location.href='player.php?id=<?php echo $book['id']; ?>'">Listen Now</button>
                    <button onclick="window.location.href='save-process.php?id=<?php echo $book['id']; ?>'">Save to Library</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
