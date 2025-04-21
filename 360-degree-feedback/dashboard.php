<?php
require_once 'includes/auth.php';
require 'db.php';
session_start();

// Check if user is logged in
requireLogin();

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$userDetails = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch user's feedback statistics
$stmt = $conn->prepare("SELECT 
    COUNT(*) as total_feedback,
    AVG(COALESCE(af.impact_rating, 0)) as avg_impact,
    AVG(COALESCE(af.accuracy_rating, 0)) as avg_accuracy,
    AVG(COALESCE(af.clarity_rating, 0)) as avg_clarity
    FROM article_feedback af 
    JOIN articles a ON af.article_id = a.id 
    WHERE af.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch user's recent feedback
$stmt = $conn->prepare("SELECT af.*, a.title as news_title 
    FROM article_feedback af 
    JOIN articles a ON af.article_id = a.id 
    WHERE af.user_id = ? 
    ORDER BY af.created_at DESC LIMIT 5");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$recentFeedback = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard - 360° Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-700 to-purple-900 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="text-xl font-bold">360° Feedback Portal</div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="hover:text-blue-200 transition">Home</a>
                    <a href="news.php" class="hover:text-blue-200 transition">News Stories</a>
                    <a href="feedback.php" class="hover:text-blue-200 transition">Submit Feedback</a>
                    <a href="analysis.php" class="hover:text-blue-200 transition">Analysis</a>
                    <a href="about.html" class="hover:text-blue-200 transition">About</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-300 font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- User Profile Card -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-purple-600 text-white flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                            <?php echo strtoupper(substr($userDetails['full_name'], 0, 1)); ?>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($userDetails['full_name']); ?></h2>
                        <p class="text-gray-600"><?php echo htmlspecialchars($userDetails['email']); ?></p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="font-semibold text-gray-700 mb-2">Account Details</h3>
                        <ul class="space-y-2">
                            <li class="flex justify-between text-sm">
                                <span class="text-gray-600">Member Since</span>
                                <span class="text-gray-800"><?php echo date('M d, Y', strtotime($userDetails['created_at'])); ?></span>
                            </li>
                            <li class="flex justify-between text-sm">
                                <span class="text-gray-600">Status</span>
                                <span class="text-green-600">Active</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Statistics and Recent Activity -->
            <div class="md:col-span-3">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-gray-500 text-sm">Total Feedback</h3>
                        <p class="text-2xl font-bold text-gray-800"><?php echo (int)$stats['total_feedback']; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-gray-500 text-sm">Avg. Impact Rating</h3>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['avg_impact'] ? number_format($stats['avg_impact'], 1) : 'N/A'; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-gray-500 text-sm">Avg. Accuracy Rating</h3>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['avg_accuracy'] ? number_format($stats['avg_accuracy'], 1) : 'N/A'; ?></p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="text-gray-500 text-sm">Avg. Clarity Rating</h3>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['avg_clarity'] ? number_format($stats['avg_clarity'], 1) : 'N/A'; ?></p>
                    </div>
                </div>

                <!-- Recent Feedback -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Feedback</h2>
                    <?php if (empty($recentFeedback)): ?>
                        <p class="text-gray-600">No feedback submitted yet.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recentFeedback as $feedback): ?>
                                <div class="border-b border-gray-200 pb-4">
                                    <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($feedback['news_title']); ?></h3>
                                    <div class="flex gap-4 text-sm text-gray-600 mt-2">
                                        <span>Impact: <?php echo $feedback['impact_rating'] ?? 'N/A'; ?>/5</span>
                                        <span>Accuracy: <?php echo $feedback['accuracy_rating'] ?? 'N/A'; ?>/5</span>
                                        <span>Clarity: <?php echo $feedback['clarity_rating'] ?? 'N/A'; ?>/5</span>
                                    </div>
                                    <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                                    <p class="text-sm text-gray-500 mt-2">Submitted on <?php echo date('M d, Y', strtotime($feedback['created_at'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
