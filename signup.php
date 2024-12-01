<?php
require 'db.php';  // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
    } else {
        // Generate a random salt (4-5 characters)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_=+[]{}|;:,.<>?/`~';
        $salt = '';
        $length = rand(4, 5);

        for ($i = 0; $i < $length; $i++) {
            $salt .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Concatenate the salt with the password
        $passwordWithSalt = $password . $salt;

        // Hash the password with the salt
        $hashedPassword = password_hash($passwordWithSalt, PASSWORD_BCRYPT);

        try {
            // Insert user into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, salt) VALUES (:username, :email, :password, :salt)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':salt' => $salt
            ]);

            echo "Sign-up successful! You can now <a href='login.php'>log in</a>.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {  // Duplicate entry error code
                echo "Username or email already exists.";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>
    <h2>Sign Up</h2>
    <form method="post" action="signup.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
