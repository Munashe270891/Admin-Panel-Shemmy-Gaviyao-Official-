<?php
require_once '../../config.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $type = $_POST['type'] ?? '';

    $tableMap = [
        'audio' => ['table' => 'audio_tracks', 'file_col' => 'audio_url', 'img_col' => 'thumbnail_url'],
        'video' => ['table' => 'videos', 'file_col' => '', 'img_col' => ''],
        'devotion' => ['table' => 'devotionals', 'file_col' => '', 'img_col' => 'image_url'],
        'book' => ['table' => 'books', 'file_col' => 'pdf_file', 'img_col' => 'cover_image']
    ];

    if ($id > 0 && isset($tableMap[$type])) {
        $info = $tableMap[$type];
        $table = $info['table'];

        // Fetch file paths first so we can unlink them from server directory
        $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        $record = $stmt->fetch();

        if ($record) {
            // Delete physical files if they exist locally
            if (!empty($info['file_col']) && !empty($record[$info['file_col']])) {
                $filePath = '../../' . $record[$info['file_col']];
                if (file_exists($filePath)) @unlink($filePath);
            }
            if (!empty($info['img_col']) && !empty($record[$info['img_col']])) {
                $imgPath = '../../' . $record[$info['img_col']];
                if (file_exists($imgPath)) @unlink($imgPath);
            }

            // Delete database row
            $delStmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
            $delStmt->execute([$id]);
        }
    }
}

header('Location: ../index.php?tab=manage');
exit;
?>
