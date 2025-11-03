<?php
session_start();
include './includes/db_connect.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'contractor') {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

$user_id = $_SESSION['user_id'];
$photo = $_POST['photo'] ?? '';

if (empty($photo)) {
    echo "No photo specified";
    exit();
}

// Fetch existing photos
$sql = "SELECT work_photos FROM contractor_details WHERE user_id='$user_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $photos = explode(',', $row['work_photos']);
    $photos = array_filter($photos, fn($p) => trim($p) !== $photo);
    $updated_photos = implode(',', $photos);

    // Update DB
    $update_sql = "UPDATE contractor_details SET work_photos='$updated_photos' WHERE user_id='$user_id'";
    mysqli_query($conn, $update_sql);

    // Delete file from folder
    $file_path = "uploads/" . $photo;
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    echo "Deleted";
} else {
    echo "No record found";
}
?>