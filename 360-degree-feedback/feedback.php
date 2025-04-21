<?php
require 'db.php';
require_once 'includes/auth.php';
session_start();

// Check if user is logged in
requireLogin();

// Get the title from URL parameter and decode it
$prefilled_title = isset($_GET['title']) ? urldecode($_GET['title']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // First, get the article ID based on the title, or create it if it doesn't exist
    $news_title = isset($_POST['news_title_hidden']) ? $_POST['news_title_hidden'] : $_POST['news_title'];
    
    // Try to find the article
    $stmt = $conn->prepare("SELECT id FROM articles WHERE title = ?");
    $stmt->bind_param("s", $news_title);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $article = $result->fetch_assoc();
        $article_id = $article['id'];
    } else {
        // Article doesn't exist, create it
        $stmt = $conn->prepare("INSERT INTO articles (title, content, author) VALUES (?, ?, ?)");
        $content = "Article content will be updated later";
        $author = "System";
        $stmt->bind_param("sss", $news_title, $content, $author);
        if ($stmt->execute()) {
            $article_id = $stmt->insert_id;
        } else {
            $_SESSION['error'] = "Error creating article: " . $stmt->error;
            header("Location: feedback.php");
            exit();
        }
    }
    $stmt->close();
    
    // Now insert the feedback
    $stmt = $conn->prepare("INSERT INTO article_feedback (user_id, article_id, feedback, impact_rating, accuracy_rating, clarity_rating) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("iisiii", 
            $_SESSION['user_id'],
            $article_id,
            $_POST['feedback'],
            $_POST['impact_rating'],
            $_POST['accuracy_rating'],
            $_POST['clarity_rating']
        );
        
        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['message'] = "Thank you for your valuable feedback!";
            header("Location: feedback.php");
            exit();
        } else {
            $_SESSION['error'] = "Error submitting feedback: " . $stmt->error;
        }
    } else {
        $_SESSION['error'] = "Error preparing statement: " . $conn->error;
    }
    $stmt->close();
}

// Get list of articles for the dropdown
$articles = [];
$result = $conn->query("SELECT title FROM articles ORDER BY title");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row['title'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback - 360° Feedback Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/main.js" defer></script>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-700 to-purple-900 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-xl font-bold">360° Feedback Portal</div>
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="hover:text-blue-200 transition nav-link">Home</a>
                    <a href="news.php" class="hover:text-blue-200 transition nav-link">News Stories</a>
                    <a href="analysis.php" class="hover:text-blue-200 transition nav-link">Analysis</a>
                    <a href="about.html" class="hover:text-blue-200 transition nav-link">About</a>
                </div>
                <button class="md:hidden" id="mobile-menu-button">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="md:hidden hidden bg-purple-600 text-white" id="mobile-menu">
        <div class="container mx-auto px-6 py-4 space-y-3">
            <a href="index.php" class="block hover:text-blue-200">Home</a>
            <a href="news.php" class="block hover:text-blue-200">News Stories</a>
            <a href="analysis.php" class="block hover:text-blue-200">Analysis</a>
            <a href="about.html" class="block hover:text-blue-200">About</a>
        </div>
    </div>

    <!-- Feedback Form Section -->
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold mb-8 text-gray-800">Submit Your Feedback</h1>

            <?php if (isset($_SESSION['message'])): ?>
                <div id="success-message"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div id="error-message"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="feedback.php" method="POST" class="bg-white shadow-md rounded-lg p-6 space-y-6">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="news_title">
                        Article Title
                    </label>
                    <?php if ($prefilled_title): ?>
                        <input type="hidden" name="news_title_hidden" value="<?php echo htmlspecialchars($prefilled_title); ?>">
                        <div class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-gray-100">
                            <?php echo htmlspecialchars($prefilled_title); ?>
                        </div>
                    <?php else: ?>
                        <select
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="news_title" name="news_title" required>
                            <option value="">Select article</option>
                            <?php foreach ($articles as $article): ?>
                                <option value="<?php echo htmlspecialchars($article); ?>"><?php echo htmlspecialchars($article); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="impact_rating">
                            Impact Rating
                        </label>
                        <select
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="impact_rating" name="impact_rating" required>
                            <option value="">Select rating</option>
                            <option value="1">1 - Low Impact</option>
                            <option value="2">2 - Moderate Impact</option>
                            <option value="3">3 - Average Impact</option>
                            <option value="4">4 - High Impact</option>
                            <option value="5">5 - Significant Impact</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="accuracy_rating">
                            Accuracy Rating
                        </label>
                        <select
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="accuracy_rating" name="accuracy_rating" required>
                            <option value="">Select rating</option>
                            <option value="1">1 - Not Accurate</option>
                            <option value="2">2 - Somewhat Accurate</option>
                            <option value="3">3 - Moderately Accurate</option>
                            <option value="4">4 - Very Accurate</option>
                            <option value="5">5 - Completely Accurate</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="clarity_rating">
                            Clarity Rating
                        </label>
                        <select
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="clarity_rating" name="clarity_rating" required>
                            <option value="">Select rating</option>
                            <option value="1">1 - Unclear</option>
                            <option value="2">2 - Somewhat Clear</option>
                            <option value="3">3 - Moderately Clear</option>
                            <option value="4">4 - Very Clear</option>
                            <option value="5">5 - Extremely Clear</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="feedback">
                        Detailed Feedback
                        <span class="font-normal text-gray-600 text-xs ml-1">Please provide your thoughts on the article's content, coverage, and presentation</span>
                    </label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="feedback" name="feedback" rows="4" required></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 ease-in-out transform hover:scale-105"
                        type="submit">
                        Submit Feedback
                    </button>
                    <span class="text-sm text-gray-600">All fields are required</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-8">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4">About Us</h4>
                    <p class="text-gray-400">A platform for collecting comprehensive feedback on news stories.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="news.php" class="hover:text-white">Latest News</a></li>
                        <li><a href="analysis.php" class="hover:text-white">View Analysis</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <p class="text-gray-400">Email: contact@360feedback.com</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 360° Feedback Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Hide the success message after 5 minutes (300,000 milliseconds)
        setTimeout(function () {
            var successMessage = document.getElementById("success-message");
            if (successMessage) {
                successMessage.style.display = "none";
            }
        }, 3000); // 300000 ms = 5 minutes
    </script>
</body>

</html>