<?php
require_once '../../config.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $topic = trim($_POST['subtitle'] ?? 'Sunday Service');
    $description = trim($_POST['description'] ?? '');

    $audioUrl = '';
    $thumbUrl = '';

    // Handle Audio File Upload
    if (isset($_FILES['audioFile']) && $_FILES['audioFile']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileName = time() . '_' . basename($_FILES['audioFile']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['audioFile']['tmp_name'], $targetPath)) {
            $audioUrl = 'uploads/' . $fileName;
        }
    }

    // Handle Thumbnail Upload
    if (isset($_FILES['audioImage']) && $_FILES['audioImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        $imgName = time() . '_img_' . basename($_FILES['audioImage']['name']);
        $targetImgPath = $uploadDir . $imgName;

        if (move_uploaded_file($_FILES['audioImage']['tmp_name'], $targetImgPath)) {
            $thumbUrl = 'uploads/' . $imgName;
        }
    }

    if (!empty($title) && !empty($audioUrl)) {
        $stmt = $pdo->prepare("INSERT INTO audio_tracks (title, topic, audio_url, thumbnail_url, publish_date) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt->execute([$title, $topic, $audioUrl, $thumbUrl]);
    }
}

header('Location: ../index.php?tab=audio');
exit;
?>
