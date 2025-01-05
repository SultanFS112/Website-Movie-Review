<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'movie_reviews');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_title = $_POST['movie_title'];
    $review_content = $_POST['review_content'];
    $rating = intval($_POST['rating']);
    $reviewer_name = $_SESSION['user'];

    // Handle File Upload
    $poster_path = '';
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $poster_tmp = $_FILES['poster']['tmp_name'];
        $poster_name = basename($_FILES['poster']['name']);
        $poster_path = 'uploads/' . time() . '_' . $poster_name;

        // Buat folder jika belum ada
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Pindahkan file ke folder uploads
        if (!move_uploaded_file($poster_tmp, $poster_path)) {
            $poster_path = ''; // Kosongkan jika upload gagal
        }
    }

    // Simpan data ke database
    $stmt = $conn->prepare("INSERT INTO reviews (movie_title, review_content, rating, reviewer_name, poster) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssiss', $movie_title, $review_content, $rating, $reviewer_name, $poster_path);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Error submitting review.";
    }

    $stmt->close();
}

$conn->close();
?>
