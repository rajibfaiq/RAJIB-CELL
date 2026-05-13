<?php
require 'vendor/autoload.php';
// Define path to writable
$dbPath = __DIR__ . '/../writable/simrs.db';

try {
    $db = new PDO("sqlite:$dbPath");
    $stmt = $db->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
