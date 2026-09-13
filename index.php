<?php
require_once '../config.php';
require_once 'auth.php'; // Ensures only logged-in admins can access

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Content Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1a1a2e; --accent: #d4af37; --bg: #f4f6f9; --white: #ffffff; --success: #2ecc71; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); margin: 0; padding: 20px; color: #333; }
        .container { max-width: 800px; margin: auto; background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { color: var(--primary); text-align: center; font-size: 1.5rem; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #777; font-size: 0.9rem; margin-bottom: 25px; }
        .alert { background: #e8f8f5; color: var(--success); padding: 12px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid #a3e4d7; }
        .tabs { display: flex; gap: 5px; margin-bottom: 25px; border-bottom: 2px solid #eee; padding-bottom: 10px; flex-wrap: wrap; justify-content: center; }
        .tab-btn { background: #e1e8ed; border: none; padding: 10px 15px; cursor: pointer; font-weight: bold; border-radius: 4px; color: var(--primary); transition: 0.2s; }
        .tab-btn.active, .tab-btn:hover { background: var(--accent); color: var(--primary); }
        .form-section { display: none; }
        .form-section.active { display: block; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 0.95rem; }
        .form-group input[type="text"], .form-group input[type="file"], .form-group textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-size: 1rem;
        }
        .form-group textarea { height: 120px; resize: vertical; }
        .btn-submit { background: var(--accent); color: var(--primary); border: none; padding: 12px; width: 100%; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 1rem; }
        .logout-link { text-align: right; margin-bottom: 15px; }
        .logout-link a { color: #e74c3c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="logout-link"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    <h1>Admin Content Upload Hub</h1>
    <p class="subtitle">Secure management portal</p>

    <?php if (!empty($message)): ?>
        <div class="alert"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('audio', event)"><i class="fas fa-podcast"></i> Audio Sermon</button>
        <button class="tab-btn" onclick="switchTab('video', event)"><i class="fas fa-video"></i> YouTube Video</button>
        <button class="tab-btn" onclick="switchTab('devotion', event)"><i class="fas fa-book-open"></i> Devotional</button>
        <button class="tab-btn" onclick="switchTab('book', event)"><i class="fas fa-book"></i> Book & PDF</button>
    </div>

    <!-- 1. Audio Form -->
    <div id="audio-form" class="form-section active">
        <form action="actions/save-audio.php" method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Audio File (.mp3)</label><input type="file" name="audioFile" accept="audio/*" required></div>
            <div class="form-group"><label>Thumbnail Cover Image</label><input type="file" name="audioImage" accept="image/*"></div>
            <div class="form-group"><label>Sermon Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Topic / Subtitle</label><input type="text" name="subtitle"></div>
            <div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
            <button type="submit" class="btn-submit">Save Audio</button>
        </form>
    </div>

    <!-- 2. Video Form -->
    <div id="video-form" class="form-section">
        <form action="actions/save-video.php" method="POST">
            <div class="form-group"><label>YouTube Link</label><input type="text" name="youtubeUrl" required></div>
            <div class="form-group"><label>Video Title</label><input type="text" name="title" required></div>
            <button type="submit" class="btn-submit">Save Video</button>
        </form>
    </div>

    <!-- 3. Devotion Form -->
    <div id="devotion-form" class="form-section">
        <form action="actions/save-devotion.php" method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Accompanying Image</label><input type="file" name="devotionImage" accept="image/*"></div>
            <div class="form-group"><label>Devotional Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Subtitle / Badge</label><input type="text" name="subtitle"></div>
            <div class="form-group"><label>Content</label><textarea name="description" required></textarea></div>
            <button type="submit" class="btn-submit">Save Devotional</button>
        </form>
    </div>

    <!-- 4. Book Form -->
    <div id="book-form" class="form-section">
        <form action="actions/save-book.php" method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Cover Image</label><input type="file" name="bookImage" accept="image/*" required></div>
            <div class="form-group"><label>PDF Document</label><input type="file" name="bookPdf" accept="application/pdf" required></div>
            <div class="form-group"><label>Book Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" required></textarea></div>
            <button type="submit" class="btn-submit">Save Book</button>
        </form>
    </div>
</div>

<script>
    function switchTab(tabName, event) {
        document.querySelectorAll('.form-section').forEach(sec => sec.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(tabName + '-form').classList.add('active');
        event.currentTarget.classList.add('active');
    }
</script>
</body>
</html>
