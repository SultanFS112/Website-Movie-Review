<?php
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Periksa apakah ID review ada di URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $username = $_SESSION['user'];

    // Koneksi ke database
    $conn = new mysqli('localhost', 'root', '', 'movie_reviews');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Hapus hanya review milik user yang login
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ? AND reviewer_name = ?");
    $stmt->bind_param('is', $id, $username);

    if ($stmt->execute()) {
        // Jika berhasil dihapus, redirect ke index.php
        header('Location: index.php');
        exit;
    } else {
        echo "Error deleting review.";
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: index.php');
    exit;
}
?>
