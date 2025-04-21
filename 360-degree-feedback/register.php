<?php
include 'db.php';

$registered = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $conn->real_escape_string($_POST['name']);
    $email    = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $error = "Email already registered. Please use a different email.";
    } else {
        // Use prepared statement for insertion
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'user', 'active')");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $registered = true;
            session_start();
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_name'] = $name;
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/register.css">
</head>
<body class="auth-background flex items-center justify-center min-h-screen px-4">
    <div class="auth-container p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Register</h2>

        <?php if ($registered): ?>
            <div class="min-h-screen w-full flex items-center justify-center">
                <div class="auth-container p-8 rounded-lg shadow-lg max-w-md w-full text-center">
                    <script>
                        alert("Registered successfully! Redirecting to login page...");
                        window.location.href = "login.php"; // redirect to login
                    </script>
                </div>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= $error ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="name" id="name" required
                    class="mt-1 w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none bg-purple-50">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                    class="mt-1 w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none bg-blue-50">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-pink-400 focus:outline-none bg-pink-50">
            </div>
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 text-white font-semibold py-2 px-4 rounded hover:from-blue-600 hover:to-pink-600 transition">Register</button>
        </form>

        <p class="text-sm text-center mt-4">Already have an account? 
            <a href="login.php" class="text-pink-600 font-medium hover:underline">Login here</a>
        </p>
    </div>
</body>
</html>
