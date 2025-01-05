<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Review Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo-container">
        <img src="logo.png" alt="Movie Review Hub Logo" class="logo">
    </div>
    <h1 class="site-title">Movie Review Hub</h1>
    <nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="add_review.php">Add Review</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

</header>


    <main>
    <h2 style="color: black;">Latest Reviews</h2>
        <div class="reviews">
            <?php
            $conn = new mysqli('localhost', 'root', '', 'movie_reviews');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM reviews ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='review-container'>";
                    
                    // Menampilkan poster jika ada
                    if (!empty($row['poster'])) {
                        echo "<div class='poster'>";
                        echo "<img src='" . htmlspecialchars($row['poster']) . "' alt='Poster' style='width:150px;height:auto;'>";
                        echo "</div>";
                    }
                
                    // Menampilkan review di sebelah kanan poster
                    echo "<div class='review-content'>";
                    echo "<h3>" . htmlspecialchars($row['movie_title']) . "</h3>";
                    echo "<p><strong>Reviewer:</strong> " . htmlspecialchars($row['reviewer_name']) . "</p>";
                    echo "<p>" . htmlspecialchars($row['review_content']) . "</p>";
                    echo "<p><em>Rating: " . htmlspecialchars($row['rating']) . "/10</em></p>";
                    
                    if (isset($_SESSION['user']) && $_SESSION['user'] === $row['reviewer_name']) {
                        echo "<a href='edit_review.php?id=" . $row['id'] . "'>Edit Review</a> | ";
                        echo "<a href='delete_review.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this review?\");'>Delete Review</a>";
                    }
                    
                
                    echo "</div>"; // penutup div review-content
                    echo "</div>"; // penutup div review-container
                }
                
                
            } else {
                echo "<p>No reviews available.</p>";
            }

            $conn->close();
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Movie Review Hub. All rights reserved.</p>
    </footer>
</body>
</html>