<?php
// Database Credentials for cPanel
$db_host = 'localhost';
$db_user = 'upnodeco_discipleship-user';
$db_pass = 'removeit'; // Replace with your actual database password
$db_name = 'upnodeco_discipleship_nation';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// Dynamic Base URL detection for cPanel subdomains/subfolders
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', rtrim($protocol . "://" . $host . ($scriptDir === '/' || $scriptDir === '\\' ? '' : $scriptDir), '/'));
?>
