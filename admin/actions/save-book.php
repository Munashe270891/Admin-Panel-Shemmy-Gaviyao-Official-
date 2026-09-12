<?php
require_once '../../config.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    $coverUrl = '';
    $pdfUrl = '';
    $uploadDir = '../../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Cover Image
    if (isset($_FILES['bookImage']) && $_FILES['bookImage']['error'] === UPLOAD_ERR_OK) {
        $imgName = time() . '_book_img_' . basename($_FILES['bookImage']['name']);
        if (move_uploaded_file($_FILES['bookImage']['tmp_name'], $uploadDir . $imgName)) {
            $coverUrl = 'uploads/' . $imgName;
        }
    }

    // PDF Document
    if (isset($_FILES['bookPdf']) && $_FILES['bookPdf']['error'] === UPLOAD_ERR_OK) {
        $pdfName = time() . '_book_' . basename($_FILES['bookPdf']['name']);
        if (move_uploaded_file($_FILES['bookPdf']['tmp_name'], $uploadDir . $pdfName)) {
            $pdfUrl = 'uploads/' . $pdfName;
        }
    }

    if (!empty($title) && !empty($pdfUrl)) {
        $stmt = $pdo->prepare("INSERT INTO books (title, description, cover_image, pdf_file) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $coverUrl, $pdfUrl]);
    }
}

header('Location: ../index.php?tab=book');
exit;
?>
