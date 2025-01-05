<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'movie_reviews');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$review_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$review = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_title = $_POST['movie_title'];
    $review_content = $_POST['review_content'];
    $rating = intval($_POST['rating']);
    $poster_path = $review['poster'];

    // Handle File Upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $poster_tmp = $_FILES['poster']['tmp_name'];
        $poster_name = basename($_FILES['poster']['name']);
        $poster_path = 'uploads/' . time() . '_' . $poster_name;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        move_uploaded_file($poster_tmp, $poster_path);
    }

    $stmt = $conn->prepare("UPDATE reviews SET movie_title = ?, review_content = ?, rating = ?, poster = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $movie_title, $review_content, $rating, $poster_path, $review_id);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Error updating review.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1a1a1a;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        .container {
            display: flex;
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            max-width: 800px;
            width: 100%;
            position: relative;
        }

        .poster {
            flex: 1;
            text-align: center;
            margin-right: 20px;
            background-color: #d3d3d3;
            color: #000;
            border-radius: 10px;
            padding: 20px;
        }

        .poster img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .form-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-section label {
            font-size: 14px;
            font-weight: bold;
        }

        .form-section input,
        .form-section textarea,
        .form-section button {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 14px;
        }

        .form-section input,
        .form-section textarea {
            background-color: #333;
            color: #fff;
        }

        .form-section textarea {
            height: 100px;
            resize: none;
        }

        .form-section button {
            background-color: #4e54c8;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .form-section button:hover {
            background-color: #3b43a0;
        }

        .back-button {
            position: absolute;
            top: 10px;
            left: -120px;
            background-color: #ff4d4d;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .back-button:hover {
            background-color: #d93636;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Tombol Back -->
        <a href="index.php" class="back-button">Back</a>

        <!-- Poster Section -->
        <div class="poster">
            <?php if (!empty($review['poster'])): ?>
                <img src="<?php echo htmlspecialchars($review['poster']); ?>" alt="Poster">
            <?php else: ?>
                <p>No Poster Available</p>
            <?php endif; ?>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <form action="edit_review.php?id=<?php echo $review_id; ?>" method="POST" enctype="multipart/form-data">
                <label for="movie_title">Movie Title</label>
                <input type="text" id="movie_title" name="movie_title" value="<?php echo htmlspecialchars($review['movie_title']); ?>" required>

                <label for="review_content">Review</label>
                <textarea id="review_content" name="review_content" required><?php echo htmlspecialchars($review['review_content']); ?></textarea>

                <label for="rating">Rating</label>
                <input type="number" id="rating" name="rating" value="<?php echo htmlspecialchars($review['rating']); ?>" min="1" max="10" required>

                <label for="poster">Poster</label>
                <input type="file" id="poster" name="poster" accept="image/*">

                <button type="submit">UPDATE</button>
            </form>
        </div>
    </div>
</body>
</html>