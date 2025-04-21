<?php
session_start();
include 'db.php';

$error = "";
$showWelcome = false;
$userName = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $userName = $user['full_name'];
            $showWelcome = true;

            // Update last login time
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            $updateStmt->close();

            // Redirect to index.php after 2 seconds
            header("Refresh: 2; URL=index.php");
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="auth-background flex items-center justify-center min-h-screen px-4">

<?php if ($showWelcome): ?>
    <div class="min-h-screen w-full flex items-center justify-center">
        <div class="auth-container p-8 rounded-lg shadow-lg max-w-md w-full text-center">
            <h2 class="text-2xl font-bold mb-4">Welcome, <?= htmlspecialchars($userName) ?>!</h2>
            <p class="mb-4">Redirecting you to home page...</p>
            <div class="loader border-4 border-gray-200 border-t-gray-600 rounded-full w-10 h-10 mx-auto animate-spin"></div>
        </div>
    </div>
<?php else: ?>
    <div class="auth-container p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= $error ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
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
                class="w-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 text-white font-semibold py-2 px-4 rounded hover:from-blue-600 hover:to-pink-600 transition">Login</button>
        </form>

        <p class="text-sm text-center mt-4">Don't have an account?
            <a href="register.php" class="text-pink-600 font-medium hover:underline">Register here</a>
        </p>
    </div>
<?php endif; ?>

</body>
</html>
