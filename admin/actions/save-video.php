<?php
require_once '../../config.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $youtubeUrl = trim($_POST['youtubeUrl'] ?? '');
    $title = trim($_POST['title'] ?? '');
    
    // Extract YouTube ID from URL helper function
    $youtubeId = '';
    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtubeUrl, $match)) {
        $youtubeId = $match[1];
    }

    if (!empty($title) && !empty($youtubeId)) {
        $stmt = $pdo->prepare("INSERT INTO videos (title, youtube_id, duration) VALUES (?, ?, '5:25')");
        $stmt->execute([$title, $youtubeId]);
    }
}

header('Location: ../index.php?tab=video');
exit;
?>
