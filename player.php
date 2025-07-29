<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM audiobooks WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo "<script>window.location.href='library.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT progress FROM user_library WHERE user_id = ? AND audiobook_id = ?");
    $stmt->execute([$_SESSION['user_id'], $_GET['id']]);
    $progress = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<script>alert('An error occurred. Please try again later.'); window.location.href='library.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listen - <?php echo htmlspecialchars($book['title']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #1a1a2e, #16213e); color: #fff; margin: 0; }
        header { background: #0f3460; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 28px; color: #e94560; }
        nav a { color: #e94560; margin: 0 15px; text-decoration: none; }
        nav a:hover { text-decoration: underline; }
        .container { padding: 20px; max-width: 800px; margin: auto; text-align: center; }
        .player { background: #0f3460; padding: 20px; border-radius: 10px; }
        .player img { max-width: 200px; border-radius: 5px; }
        .player audio { width: 100%; margin: 20px 0; }
        .controls button { background: #e94560; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .controls button:hover { background: #d43f52; }
        .progress { margin: 10px 0; }
        @media (max-width: 600px) { .player img { max-width: 150px; } }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($book['title']); ?></h1>
        <nav>
            <a href="javascript:window.location.href='index.php'">Home</a>
            <a href="javascript:window.location.href='library.php'">Library</a>
            <a href="javascript:window.location.href='dashboard.php'">Dashboard</a>
            <a href="javascript:window.location.href='logout.php'">Logout</a>
        </nav>
    </header>
    <div class="container">
        <div class="player">
            <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
            <h2><?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?></h2>
            <audio id="audio" src="<?php echo htmlspecialchars($book['audio_file']); ?>" controls></audio>
            <div class="controls">
                <button onclick="changeSpeed(1)">1x</button>
                <button onclick="changeSpeed(1.5)">1.5x</button>
                <button onclick="changeSpeed(2)">2x</button>
                <button onclick="rewind()">Rewind 10s</button>
                <button onclick="forward()">Forward 10s</button>
            </div>
            <div class="progress">Progress: <span id="progress"><?php echo $progress; ?></span>%</div>
        </div>
    </div>
    <script>
        const audio = document.getElementById('audio');
        audio.currentTime = (<?php echo $progress; ?> / 100) * audio.duration || 0;

        audio.ontimeupdate = () => {
            if (!audio.duration) return; // Prevent NaN errors
            const progress = (audio.currentTime / audio.duration) * 100;
            document.getElementById('progress').textContent = Math.floor(progress);
            fetch(`save-process.php?id=<?php echo $_GET['id']; ?>&progress=${Math.floor(progress)}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).catch(error => console.error('Error saving progress:', error));
        };

        function changeSpeed(speed) {
            audio.playbackRate = speed;
        }

        function rewind() {
            audio.currentTime = Math.max(0, audio.currentTime - 10);
        }

        function forward() {
            audio.currentTime = Math.min(audio.duration, audio.currentTime + 10);
        }
    </script>
</body>
</html>
