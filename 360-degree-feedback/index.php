<?php
session_start();
require_once 'includes/auth.php';
require 'db.php';

// Get user details if logged in
$userDetails = null;
$feedbackCount = 0;
if (isLoggedIn()) {
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $userDetails = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Get feedback count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM article_feedback WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $row = $result->fetch_assoc();
        $feedbackCount = $row['count'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360° Feedback Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .news-slider-container {
            width: 100%;
            overflow: hidden;
            padding: 20px;
            position: relative;
        }
        .news-slider-track {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            animation: scroll 30s linear infinite;
            width: max-content;
            padding: 20px 0;
        }
        .news-slider-track::-webkit-scrollbar {
            display: none;
        }
        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(calc(-300px * 4));
            }
        }
        .news-slider-track:hover {
            animation-play-state: paused;
        }
        .news-card {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            transform-origin: center;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .news-card:hover {
            transform: translateY(-10px) scale(1.15);
            z-index: 20;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .news-slider-track:hover .news-card:not(:hover) {
            transform: scale(0.95);
            opacity: 0.8;
        }
        .hero-text {
            opacity: 0;
            transform: translateY(30px);
            animation: heroFadeIn 1s ease forwards;
        }
        @keyframes heroFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .gradient-text {
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: gradientMove 3s ease infinite;
        }
        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: currentColor;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        @keyframes blob {
          0% {
            transform: translate(0px, 0px) scale(1);
          }
          33% {
            transform: translate(30px, -50px) scale(1.1);
          }
          66% {
            transform: translate(-20px, 20px) scale(0.9);
          }
          100% {
            transform: translate(0px, 0px) scale(1);
          }
        }
        .animate-blob {
          animation: blob 7s infinite;
        }
        .animation-delay-2000 {
          animation-delay: 2s;
        }
        .animation-delay-4000 {
          animation-delay: 4s;
        }
        .user-menu {
            position: relative;
        }
        
        .user-menu-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            z-index: 50;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .user-menu:hover .user-menu-content {
            display: block;
            animation: slideDown 0.2s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .user-avatar {
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }
    </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="bg-gradient-to-r from-purple-700 to-purple-900 text-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4 sm:px-6 py-4">
      <div class="flex justify-between items-center">
        <div class="text-xl font-bold">360° Feedback Portal</div>
        <div class="hidden md:flex items-center space-x-6">
          <a href="index.php" class="hover:text-blue-200 transition nav-link">Home</a>
          <a href="about.html" class="hover:text-blue-200 transition nav-link">About</a>
          <a href="news.php" class="hover:text-blue-200 transition nav-link">News Stories</a>
          
          <?php if (isLoggedIn() && $userDetails): ?>
            <a href="analysis.php" class="hover:text-blue-200 transition nav-link">Analysis</a>
            <a href="dashboard.php" class="hover:text-blue-200 transition nav-link">Dashboard</a>
            
            <!-- User Menu -->
            <div class="user-menu">
                <div class="flex items-center space-x-3 cursor-pointer">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($userDetails['full_name'], 0, 1)); ?>
                    </div>
                    <span class="font-medium"><?php echo htmlspecialchars($userDetails['full_name']); ?></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="user-menu-content">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm text-gray-500">Signed in as</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($userDetails['full_name']); ?></p>
                    </div>
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total Feedback</span>
                            <span class="text-sm font-semibold text-purple-600"><?php echo $feedbackCount; ?></span>
                        </div>
                    </div>
                    <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sign out</a>
                </div>
            </div>
          <?php else: ?>
            <div class="flex items-center space-x-4">
                <a href="register.php" class="bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50 transition-colors duration-300 font-medium">Register</a>
                <a href="login.php" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors duration-300 font-medium">Login</a>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Mobile menu button -->
        <button class="md:hidden text-white focus:outline-none" id="mobile-menu-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
        </button>
      </div>

      <!-- Mobile menu -->
      <div class="md:hidden hidden" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <a href="index.php" class="block px-3 py-2 text-white hover:bg-purple-600 rounded-md">Home</a>
          <a href="about.html" class="block px-3 py-2 text-white hover:bg-purple-600 rounded-md">About</a>
          <a href="news.php" class="block px-3 py-2 text-white hover:bg-purple-600 rounded-md">News Stories</a>
          <?php if (isLoggedIn() && $userDetails): ?>
            <a href="analysis.php" class="block px-3 py-2 text-white hover:bg-purple-600 rounded-md">Analysis</a>
            <a href="dashboard.php" class="block px-3 py-2 text-white hover:bg-purple-600 rounded-md">Dashboard</a>
            <div class="border-t border-purple-600 my-2"></div>
            <div class="px-3 py-2">
                <p class="text-sm text-purple-200">Signed in as</p>
                <p class="font-medium text-white"><?php echo htmlspecialchars($userDetails['full_name']); ?></p>
                <p class="text-sm text-purple-200 mt-1">Total Feedback: <span class="font-medium"><?php echo $feedbackCount; ?></span></p>
            </div>
            <a href="logout.php" class="block px-3 py-2 text-red-200 hover:bg-red-600 rounded-md">Sign out</a>
          <?php else: ?>
            <div class="border-t border-purple-600 my-2"></div>
            <a href="register.php" class="block px-3 py-2 text-white hover:bg-purple-600 rounded-md">Register</a>
            <a href="login.php" class="block px-3 py-2 text-white hover:bg-purple-600 rounded-md">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div class="md:hidden hidden bg-purple-800 text-white" id="mobile-menu">
    <div class="container mx-auto px-4 py-4 space-y-3">
      <a href="index.php" class="block hover:text-blue-200 transition">Home</a>
      <a href="about.html" class="block hover:text-blue-200 transition">About</a>
      <a href="news.php" class="block hover:text-blue-200 transition">News Stories</a>
      <?php if (isLoggedIn() && $userDetails): ?>
        <a href="analysis.php" class="block hover:text-blue-200 transition">Analysis</a>
        <a href="dashboard.php" class="block hover:text-blue-200 transition">Dashboard</a>
        <a href="logout.php" class="block bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-300 font-medium mt-2 inline-block">Logout</a>
      <?php else: ?>
        <a href="register.php" class="block bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50 transition-colors duration-300 font-medium mt-4 inline-block">Register</a>
        <a href="login.php" class="block bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors duration-300 font-medium mt-2 inline-block">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Hero Section -->
  <div class="bg-gradient-to-r from-purple-700 to-purple-900 text-white relative overflow-hidden">
    <!-- Animated background shapes -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute w-96 h-96 bg-purple-500 rounded-full opacity-10 -top-20 -left-20 animate-blob"></div>
      <div class="absolute w-96 h-96 bg-indigo-500 rounded-full opacity-10 -bottom-20 -right-20 animate-blob animation-delay-2000"></div>
      <div class="absolute w-96 h-96 bg-pink-500 rounded-full opacity-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-blob animation-delay-4000"></div>
    </div>
    
    <div class="relative py-24">
      <div class="container mx-auto px-6 max-w-4xl text-center">
        <div class="space-y-8">
          <h1 class="text-5xl md:text-6xl font-bold mb-6 hero-text" style="animation-delay: 0.2s">
            <span class="gradient-text">360° Feedback</span><br/>
            <span class="text-4xl md:text-5xl">on Government News Stories</span>
          </h1>
          <p class="text-xl md:text-2xl mb-12 text-purple-100 hero-text leading-relaxed" style="animation-delay: 0.4s">
            Your voice matters. Share your perspective on news stories about the<br class="hidden md:block"/> Government of India from regional media sources.
          </p>
          <div class="flex flex-col md:flex-row items-center justify-center gap-4 hero-text" style="animation-delay: 0.6s">
            <a href="feedback.php" class="group inline-flex items-center bg-white text-purple-700 px-8 py-4 rounded-lg font-semibold hover:bg-purple-50 transition transform hover:scale-105 shadow-lg">
              Start Giving Feedback
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </a>
            <a href="about.html" class="inline-flex items-center text-white border-2 border-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-purple-700 transition">
              Learn More
            </a>
          </div>
          
          <!-- Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 max-w-3xl mx-auto hero-text" style="animation-delay: 0.8s">
            <div class="text-center">
              <div class="text-4xl font-bold mb-2">500+</div>
              <div class="text-purple-200">Daily Users</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold mb-2">1000+</div>
              <div class="text-purple-200">News Stories</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold mb-2">5000+</div>
              <div class="text-purple-200">Feedbacks</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-bold mb-2">20+</div>
              <div class="text-purple-200">News Sources</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- News Section -->
  <section class="bg-gradient-to-b from-white to-purple-50 py-20">
    <div class="w-full">
      <h2 class="text-4xl font-bold text-center mb-4 gradient-text">Latest News</h2>
      <p class="text-gray-600 text-center mb-12">Stay informed with the most recent government updates</p>

      <!-- Sliding News Container -->
      <div class="news-slider-container">
        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-gradient-to-r from-white to-transparent w-32 h-full pointer-events-none"></div>
        <div class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-gradient-to-l from-white to-transparent w-32 h-full pointer-events-none"></div>
        
        <div id="news-container" class="news-slider-track">
          <!-- Cards will be injected by JS -->
        </div>
      </div>

      <!-- Status messages -->
      <div id="loading" class="text-center mt-12">
        <div class="flex items-center justify-center space-x-3">
          <div class="w-4 h-4 bg-purple-600 rounded-full animate-pulse"></div>
          <div class="w-4 h-4 bg-purple-600 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
          <div class="w-4 h-4 bg-purple-600 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
        </div>
        <p class="mt-4 text-purple-600 font-medium">Fetching latest news...</p>
      </div>
      <div id="error" class="hidden text-center text-red-500 mt-12 bg-red-50 p-4 rounded-lg max-w-md mx-auto">
        <svg class="w-8 h-8 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-lg font-medium">Failed to load news</p>
        <p class="text-sm text-red-600 mt-1">Please try again later</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-lg font-semibold mb-4">About Us</h3>
          <p class="text-gray-400">We provide a platform for citizens to give feedback on government news coverage across various media sources.</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-2">
            <li><a href="about.html" class="text-gray-400 hover:text-white transition">About</a></li>
            <li><a href="news.php" class="text-gray-400 hover:text-white transition">News</a></li>
            <li><a href="feedback.php" class="text-gray-400 hover:text-white transition">Submit Feedback</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-4">Contact</h3>
          <p class="text-gray-400">
            <a href="contact.php" class="hover:text-white transition flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              Contact Us
            </a>
            <span class="block mt-2">Email: info@lpu.co.in<br>Phone: +91 1824 404404</span>
          </p>
        </div>
      </div>
      <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2024 360° Feedback Portal. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
  </script>

  <script>
    // Fetch and display news
    async function fetchNews() {
      try {
        const API_KEY = '3fc4c60978dc4b33acde0a9904194035';
        const API_URL = `https://newsapi.org/v2/everything?q=india&sortBy=publishedAt&language=en&apiKey=${API_KEY}`;
        
        const response = await fetch(API_URL);
        const data = await response.json();
        
        if (data.status === 'ok') {
          const newsContainer = document.getElementById('news-container');
          document.getElementById('loading').style.display = 'none';
          
          // Create the initial set of articles
          const createNewsCards = (articles) => {
            articles.forEach((article, index) => {
              const card = document.createElement('div');
              card.className = 'news-card flex-shrink-0 w-80 bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl';
              card.style.animationDelay = `${index * 0.1}s`;
              card.innerHTML = `
                <div class="relative">
                  <img src="${article.urlToImage || 'placeholder.jpg'}" alt="${article.title}" class="w-full h-48 object-cover">
                  <div class="absolute top-0 right-0 bg-purple-600 text-white text-xs px-2 py-1 m-2 rounded">
                    ${new Date(article.publishedAt).toLocaleDateString()}
                  </div>
                </div>
                <div class="p-4">
                  <h3 class="font-semibold text-lg mb-2 line-clamp-2">${article.title}</h3>
                  <p class="text-gray-600 text-sm mb-4 line-clamp-3">${article.description || ''}</p>
                  <div class="flex items-center justify-between">
                    <a href="feedback.php?title=${encodeURIComponent(article.title)}" 
                       class="inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition transform hover:scale-105">
                      Give Feedback
                    </a>
                    <a href="${article.url}" target="_blank" 
                       class="text-purple-600 hover:text-purple-800 transition">
                      Read More →
                    </a>
                  </div>
                </div>
              `;
              newsContainer.appendChild(card);
            });
          };

          // Create initial set
          createNewsCards(data.articles.slice(0, 8));
          
          // Clone the cards for infinite scroll effect
          const cards = newsContainer.innerHTML;
          newsContainer.innerHTML = cards + cards;
          
        } else {
          throw new Error('Failed to fetch news');
        }
      } catch (error) {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('error').classList.remove('hidden');
        document.getElementById('error').textContent = 'Failed to load news. Please try again later.';
      }
    }

    // Load news when the page loads
    window.addEventListener('load', fetchNews);
  </script>
</body>
</html>
