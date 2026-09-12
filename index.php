<?php
require_once 'config.php';

// Handle form submissions directly from root index if posted
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'audio') {
        $title = trim($_POST['title'] ?? '');
        $topic = trim($_POST['subtitle'] ?? 'Sunday Service');
        $description = trim($_POST['description'] ?? '');
        $audioUrl = '';
        $thumbUrl = '';

        if (isset($_FILES['audioFile']) && $_FILES['audioFile']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = time() . '_' . basename($_FILES['audioFile']['name']);
            if (move_uploaded_file($_FILES['audioFile']['tmp_name'], $uploadDir . $fileName)) {
                $audioUrl = 'uploads/' . $fileName;
            }
        }
        if (isset($_FILES['audioImage']) && $_FILES['audioImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $imgName = time() . '_img_' . basename($_FILES['audioImage']['name']);
            if (move_uploaded_file($_FILES['audioImage']['tmp_name'], $uploadDir . $imgName)) {
                $thumbUrl = 'uploads/' . $imgName;
            }
        }
        if (!empty($title) && !empty($audioUrl)) {
            $stmt = $pdo->prepare("INSERT INTO audio_tracks (title, topic, audio_url, thumbnail_url, publish_date) VALUES (?, ?, ?, ?, CURDATE())");
            $stmt->execute([$title, $topic, $audioUrl, $thumbUrl]);
            $message = "Audio sermon successfully uploaded and saved to database!";
        }
    } 
    elseif ($action === 'video') {
        $title = trim($_POST['title'] ?? '');
        $youtubeUrl = trim($_POST['youtubeUrl'] ?? '');
        
        // Extract YouTube ID
        $youtubeId = '';
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtubeUrl, $match)) {
            $youtubeId = $match[1];
        } else {
            $youtubeId = $youtubeUrl; // fallback if raw ID is entered
        }

        if (!empty($title) && !empty($youtubeId)) {
            $stmt = $pdo->prepare("INSERT INTO videos (title, youtube_id) VALUES (?, ?)");
            $stmt->execute([$title, $youtubeId]);
            $message = "Video link successfully saved to database!";
        }
    }
    elseif ($action === 'devotion') {
        $title = trim($_POST['title'] ?? '');
        $badge = trim($_POST['subtitle'] ?? '');
        $content = trim($_POST['description'] ?? '');
        $imageUrl = '';

        if (isset($_FILES['devotionImage']) && $_FILES['devotionImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $imgName = time() . '_dev_' . basename($_FILES['devotionImage']['name']);
            if (move_uploaded_file($_FILES['devotionImage']['tmp_name'], $uploadDir . $imgName)) {
                $imageUrl = 'uploads/' . $imgName;
            }
        }

        if (!empty($title) && !empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO devotionals (title, badge, content, image_url, publish_date) VALUES (?, ?, ?, ?, CURDATE())");
            $stmt->execute([$title, $badge, $content, $imageUrl]);
            $message = "Devotional successfully published to database!";
        }
    }
    elseif ($action === 'book') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $coverImage = '';
        $pdfFile = '';

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        if (isset($_FILES['bookImage']) && $_FILES['bookImage']['error'] === UPLOAD_ERR_OK) {
            $imgName = time() . '_bookimg_' . basename($_FILES['bookImage']['name']);
            if (move_uploaded_file($_FILES['bookImage']['tmp_name'], $uploadDir . $imgName)) {
                $coverImage = 'uploads/' . $imgName;
            }
        }
        if (isset($_FILES['bookPdf']) && $_FILES['bookPdf']['error'] === UPLOAD_ERR_OK) {
            $pdfName = time() . '_' . basename($_FILES['bookPdf']['name']);
            if (move_uploaded_file($_FILES['bookPdf']['tmp_name'], $uploadDir . $pdfName)) {
                $pdfFile = 'uploads/' . $pdfName;
            }
        }

        if (!empty($title)) {
            $stmt = $pdo->prepare("INSERT INTO books (title, description, cover_image, pdf_file) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $coverImage, $pdfFile]);
            $message = "Book record successfully uploaded to database!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Content Upload Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a2e;
            --accent: #d4af37;
            --bg: #f4f6f9;
            --white: #ffffff;
            --success: #2ecc71;
        }
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
        .btn-submit:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="container">
    <h1>Database Content Upload Hub</h1>
    <p class="subtitle">Direct data-entry portal for publishing content to your remote website database</p>

    <?php if (!empty($message)): ?>
        <div class="alert"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('audio', event)"><i class="fas fa-podcast"></i> Audio Sermon</button>
        <button class="tab-btn" onclick="switchTab('video', event)"><i class="fas fa-video"></i> YouTube Video</button>
        <button class="tab-btn" onclick="switchTab('devotion', event)"><i class="fas fa-book-open"></i> Devotional / Blog</button>
        <button class="tab-btn" onclick="switchTab('book', event)"><i class="fas fa-book"></i> Book & PDF</button>
    </div>

    <!-- 1. Audio Form -->
    <div id="audio-form" class="form-section active">
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="audio">
            <div class="form-group">
                <label>Audio File (.mp3)</label>
                <input type="file" name="audioFile" accept="audio/*" required>
            </div>
            <div class="form-group">
                <label>Thumbnail Cover Image</label>
                <input type="file" name="audioImage" accept="image/*">
            </div>
            <div class="form-group">
                <label>Sermon Title</label>
                <input type="text" name="title" placeholder="Enter title..." required>
            </div>
            <div class="form-group">
                <label>Topic / Subtitle / Series Tag</label>
                <input type="text" name="subtitle" placeholder="e.g., Sunday Service Series">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Enter details..."></textarea>
            </div>
            <button type="submit" class="btn-submit">Save Audio to Database</button>
        </form>
    </div>

    <!-- 2. Video Form -->
    <div id="video-form" class="form-section">
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="video">
            <div class="form-group">
                <label>YouTube Video Link / URL</label>
                <input type="text" name="youtubeUrl" placeholder="https://www.youtube.com/watch?v=..." required>
            </div>
            <div class="form-group">
                <label>Video Title</label>
                <input type="text" name="title" placeholder="Enter video title..." required>
            </div>
            <button type="submit" class="btn-submit">Save Video to Database</button>
        </form>
    </div>

    <!-- 3. Devotion Form -->
    <div id="devotion-form" class="form-section">
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="devotion">
            <div class="form-group">
                <label>Accompanying Image</label>
                <input type="file" name="devotionImage" accept="image/*">
            </div>
            <div class="form-group">
                <label>Devotional Title</label>
                <input type="text" name="title" placeholder="Enter title..." required>
            </div>
            <div class="form-group">
                <label>Subtitle / Theme Badge</label>
                <input type="text" name="subtitle" placeholder="e.g., Walking in Faith">
            </div>
            <div class="form-group">
                <label>Devotional Body Text / Blog Content</label>
                <textarea name="description" placeholder="Write or paste your devotional text here..." style="height: 180px;" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Save Devotional to Database</button>
        </form>
    </div>

    <!-- 4. Book Form -->
    <div id="book-form" class="form-section">
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="book">
            <div class="form-group">
                <label>Book Cover Image</label>
                <input type="file" name="bookImage" accept="image/*" required>
            </div>
            <div class="form-group">
                <label>PDF Document File</label>
                <input type="file" name="bookPdf" accept="application/pdf" required>
            </div>
            <div class="form-group">
                <label>Book Title</label>
                <input type="text" name="title" placeholder="Enter book title..." required>
            </div>
            <div class="form-group">
                <label>Description / Blurb</label>
                <textarea name="description" placeholder="Enter overview description..." style="height: 140px;" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Save Book to Database</button>
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
