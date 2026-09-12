<?php
require_once '../../config.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $badge = trim($_POST['subtitle'] ?? 'Walking in Faith');
    $content = trim($_POST['description'] ?? '');
    $imageUrl = '';

    if (isset($_FILES['devotionImage']) && $_FILES['devotionImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileName = time() . '_dev_' . basename($_FILES['devotionImage']['name']);
        if (move_uploaded_file($_FILES['devotionImage']['tmp_name'], $uploadDir . $fileName)) {
            $imageUrl = 'uploads/' . $fileName;
        }
    }

    if (!empty($title) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO devotionals (title, badge, content, image_url, publish_date) VALUES (?, ?, ?, ?, CURDATE())");
        $stmt->execute([$title, $badge, $content, $imageUrl]);
    }
}

header('Location: ../index.php?tab=devotion');
exit;
?>
