<?php
require 'db.php';  // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "Both fields are required.";
    } else {
        try {
            // Retrieve the user's data by username
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Retrieve the stored salt and hashed password
                $salt = $user['salt'];
                $storedPassword = $user['password'];

                // Concatenate the input password with the stored salt
                $passwordWithSalt = $password . $salt;

                // Verify the hashed password
                if (password_verify($passwordWithSalt, $storedPassword)) {
                    echo "Login successful! Welcome, " . htmlspecialchars($user['username']) . ".";
                } else {
                    echo "Invalid credentials. Please try again.";
                }
            } else {
                echo "No user found with that username.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
