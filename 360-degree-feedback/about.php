<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - 360° Feedback Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/main.js" defer></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="index.php" class="text-xl font-bold hover:text-blue-200">360° Feedback Portal</a>
                <div class="hidden md:flex space-x-6">
                    <a href="index.php" class="hover:text-blue-200">Home</a>
                    <a href="news.php" class="hover:text-blue-200">News Stories</a>
                    <a href="feedback.php" class="hover:text-blue-200">Submit Feedback</a>
                    <a href="analysis.php" class="hover:text-blue-200">Analysis</a>
                    <a href="about.html" class="hover:text-blue-200 border-b-2 border-white">About</a>
                </div>
                <button class="md:hidden" id="mobile-menu-button">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="md:hidden hidden bg-blue-500 text-white" id="mobile-menu">
        <div class="container mx-auto px-6 py-4 space-y-3">
            <a href="index.php" class="block hover:text-blue-200">Home</a>
            <a href="news.php" class="block hover:text-blue-200">News Stories</a>
            <a href="feedback.php" class="block hover:text-blue-200">Submit Feedback</a>
            <a href="analysis.php" class="block hover:text-blue-200">Analysis</a>
            <a href="about.html" class="block hover:text-blue-200">About</a>
        </div>
    </div>

    <!-- About Section -->
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">About Our Platform</h1>
            
            <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                <h2 class="text-2xl font-semibold mb-4">Our Mission</h2>
                <p class="text-gray-700 mb-6">
                    The 360° Feedback Portal aims to create a comprehensive platform for collecting, analyzing, and understanding public perception of Government of India news stories across regional media sources. Our goal is to promote transparency, encourage diverse perspectives, and facilitate meaningful dialogue between the government and its citizens.
                </p>

                <h2 class="text-2xl font-semibold mb-4">Why 360-Degree Feedback?</h2>
                <p class="text-gray-700 mb-6">
                    360-degree feedback provides a holistic view of how government initiatives and news stories are perceived across different regions and demographics. This comprehensive approach helps in:
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-6 space-y-2">
                    <li>Understanding regional perspectives</li>
                    <li>Identifying communication gaps</li>
                    <li>Improving government communication strategies</li>
                    <li>Promoting transparency and accountability</li>
                </ul>

                <h2 class="text-2xl font-semibold mb-4">Our Process</h2>
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center p-4">
                        <div class="text-blue-600 text-2xl font-bold mb-2">1</div>
                        <h3 class="font-semibold mb-2">Collection</h3>
                        <p class="text-gray-600">Gather feedback from diverse sources across regions</p>
                    </div>
                    <div class="text-center p-4">
                        <div class="text-blue-600 text-2xl font-bold mb-2">2</div>
                        <h3 class="font-semibold mb-2">Analysis</h3>
                        <p class="text-gray-600">Process and analyze the collected feedback</p>
                    </div>
                    <div class="text-center p-4">
                        <div class="text-blue-600 text-2xl font-bold mb-2">3</div>
                        <h3 class="font-semibold mb-2">Insights</h3>
                        <p class="text-gray-600">Generate actionable insights and recommendations</p>
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-semibold mb-6">Our Team</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4"></div>
                        <h3 class="font-semibold">Project Lead</h3>
                        <p class="text-gray-600">Team Member 1</p>
                    </div>
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4"></div>
                        <h3 class="font-semibold">Admin</h3>
                        <p class="text-gray-600">Team Member 2</p>
                    </div>
                    <div class="text-center">
                        <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4"></div>
                        <h3 class="font-semibold">Technical Lead</h3>
                        <p class="text-gray-600">Team Member 3</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-8">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4">About Us</h4>
                    <p class="text-gray-400">A platform for collecting comprehensive feedback on Government of India news stories.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="news.php" class="hover:text-white">Latest News</a></li>
                        <li><a href="feedback.php" class="hover:text-white">Submit Feedback</a></li>
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
</body>
</html>
