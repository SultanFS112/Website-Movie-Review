<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'admin' && $password === 'password') {
        $_SESSION['user'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            font-family: Arial, sans-serif;
        }
        .login-container {
            background: #fff;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 1em;
            color: #333;
        }
        .login-container input {
            width: 100%;
            padding: 0.5em;
            margin: 0.5em 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 0.5em;
            border: none;
            border-radius: 5px;
            background: #4e54c8;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-container button:hover {
            background: #6a6ee7;
        }
        .login-container a {
            text-decoration: none;
            color: #4e54c8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <p style="color:red;"> <?= $error; ?> </p>
        <?php endif; ?>
        <p>Not a Member? <a href="signup.php">Signup</a></p>
    </div>
</body>
</html>