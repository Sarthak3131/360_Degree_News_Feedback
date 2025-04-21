<?php
require_once 'config/config.php';
require_once 'config/error_config.php';

// Initialize variables
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search = isset($_GET['search']) ? trim(htmlspecialchars($_GET['search'])) : '';
$category = isset($_GET['category']) ? trim(htmlspecialchars($_GET['category'])) : '';

// Function to fetch news from NewsAPI
function fetchNews($page = 1, $search = '', $category = '') {
    if (!function_exists('curl_init')) {
        error_log('cURL is not installed');
        return getFallbackNews();
    }

    $offset = ($page - 1) * ITEMS_PER_PAGE;
    
    // Use everything endpoint for broader search with better relevance
    $url = 'https://newsapi.org/v2/everything';
    $params = [
        'apiKey' => NEWS_API_KEY,
        'pageSize' => ITEMS_PER_PAGE,
        'page' => $page,
        'language' => 'en',
        'sortBy' => 'publishedAt'
    ];

    // Build search query
    $searchQuery = [];
    if (!empty($search)) {
        $searchQuery[] = $search;
    }

    // Add category to search query if specified
    if (!empty($category) && $category !== 'all') {
        $searchQuery[] = $category;
    }

    // Always include India-related news
    $searchQuery[] = 'India';
    
    // Add government-related terms for better relevance
    $searchQuery[] = '(government OR ministry OR policy OR initiative)';
    
    $params['q'] = implode(' AND ', $searchQuery);
    $url .= '?' . http_build_query($params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        curl_close($ch);
        return getFallbackNews();
    }
    
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (!$result || !isset($result['articles']) || isset($result['status']) && $result['status'] === 'error') {
        error_log('API Error: ' . print_r($result, true));
        return getFallbackNews();
    }

    return $result;
}

// Fallback news data when API fails
function getFallbackNews() {
    return [
        'articles' => [
            [
                'title' => 'Digital India Initiative Expands to Rural Areas',
                'source' => ['name' => 'Tech Times India'],
                'publishedAt' => date('c'),
                'description' => 'Government announces major expansion of digital services to rural areas, aiming to connect more villages to high-speed internet.',
                'urlToImage' => 'https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg',
                'url' => '#'
            ],
            [
                'title' => 'New Healthcare Policy Announced',
                'source' => ['name' => 'Health News India'],
                'publishedAt' => date('c', strtotime('-1 day')),
                'description' => 'Ministry of Health introduces comprehensive healthcare policy focusing on preventive care and digital health records.',
                'urlToImage' => 'https://images.pexels.com/photos/247786/pexels-photo-247786.jpeg',
                'url' => '#'
            ],
            [
                'title' => 'Education Reform Package Unveiled',
                'source' => ['name' => 'Education Daily'],
                'publishedAt' => date('c', strtotime('-2 days')),
                'description' => 'New education reform package aims to modernize curriculum and introduce more practical skills training.',
                'urlToImage' => 'https://images.pexels.com/photos/256417/pexels-photo-256417.jpeg',
                'url' => '#'
            ]
        ],
        'totalResults' => 3
    ];
}

// Fetch news data with error handling
try {
    $newsData = fetchNews($page, $search, $category);
    $totalResults = $newsData['totalResults'];
    $totalPages = ceil($totalResults / ITEMS_PER_PAGE);
    $news_stories = $newsData['articles'];

    // Convert NewsAPI data to our format
    $formatted_stories = [];
    foreach ($news_stories as $story) {
        $formatted_stories[] = [
            'title' => $story['title'],
            'source' => $story['source']['name'],
            'date' => date('Y-m-d', strtotime($story['publishedAt'])),
            'summary' => $story['description'] ?? 'No description available',
            'image_url' => $story['urlToImage'] ?? 'https://placehold.co/800x600/6B46C1/FFFFFF/png?text=News&font=roboto',
            'category' => $category ?: 'General',
            'url' => $story['url']
        ];
    }
} catch (Exception $e) {
    error_log('Error fetching news: ' . $e->getMessage());
    $fallbackData = getFallbackNews();
    $totalResults = $fallbackData['totalResults'];
    $totalPages = ceil($totalResults / ITEMS_PER_PAGE);
    $formatted_stories = array_map(function($story) use ($category) {
        return [
            'title' => $story['title'],
            'source' => $story['source']['name'],
            'date' => date('Y-m-d', strtotime($story['publishedAt'])),
            'summary' => $story['description'] ?? 'No description available',
            'image_url' => $story['urlToImage'] ?? 'https://placehold.co/800x600/6B46C1/FFFFFF/png?text=News&font=roboto',
            'category' => $category ?: 'General',
            'url' => $story['url']
        ];
    }, $fallbackData['articles']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Stories - 360° Feedback Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .news-card {
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .image-overlay {
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
        }
        .news-card-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .news-card-body {
            flex: 1;
        }
        .news-card-footer {
            margin-top: auto;
            border-top: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .grid-3x3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        @media (max-width: 1024px) {
            .grid-3x3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 640px) {
            .grid-3x3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        // Add debounce function for search optimization
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Wait for document to load
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const categorySelect = document.querySelector('select[name="category"]');
            const form = document.querySelector('form');

            // Function to update URL parameters without reloading
            function updateUrlParams(params) {
                const url = new URL(window.location.href);
                for (const [key, value] of Object.entries(params)) {
                    if (value) {
                        url.searchParams.set(key, value);
                    } else {
                        url.searchParams.delete(key);
                    }
                }
                window.history.pushState({}, '', url);
            }

            // Handle search input with debounce
            const handleSearch = debounce(() => {
                const searchValue = searchInput.value.trim();
                const categoryValue = categorySelect.value;
                updateUrlParams({
                    search: searchValue,
                    category: categoryValue,
                    page: '1' // Reset to first page on search
                });
                form.submit();
            }, 500);

            // Add event listeners
            if (searchInput) {
                searchInput.addEventListener('input', handleSearch);
            }
            if (categorySelect) {
                categorySelect.addEventListener('change', handleSearch);
            }
        });
    </script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-700 to-purple-900 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="text-xl font-bold">360° Feedback Portal</div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="hover:text-blue-200 transition">Home</a>
                    <a href="news.php" class="hover:text-blue-200 transition">News Stories</a>
                    <a href="feedback.php" class="hover:text-blue-200 transition">Submit Feedback</a>
                    <a href="analysis.php" class="hover:text-blue-200 transition">Analysis</a>
                    <a href="about.html" class="hover:text-blue-200 transition">About</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- News Stories Section -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-1">
        <h1 class="text-4xl font-bold mb-4 text-gray-800 text-center">Latest Government News Stories</h1>
        <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">Stay updated with the latest government initiatives, policies, and developments from across India.</p>

        <!-- Search and Filter -->
        <div class="mb-12 max-w-4xl mx-auto">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <form action="" method="GET" class="grid md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <div class="relative">
                            <input type="text" 
                                name="search"
                                value="<?php echo htmlspecialchars($search); ?>"
                                placeholder="Search news stories..." 
                                class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="md:col-span-4">
                        <select name="category" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white transition-all duration-300">
                            <option value="">All Categories</option>
                            <option value="business" <?php echo $category === 'business' ? 'selected' : ''; ?>>Business</option>
                            <option value="technology" <?php echo $category === 'technology' ? 'selected' : ''; ?>>Technology</option>
                            <option value="health" <?php echo $category === 'health' ? 'selected' : ''; ?>>Healthcare</option>
                            <option value="science" <?php echo $category === 'science' ? 'selected' : ''; ?>>Science</option>
                            <option value="sports" <?php echo $category === 'sports' ? 'selected' : ''; ?>>Sports</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <button type="submit" class="w-full bg-purple-600 text-white px-8 py-3 rounded-lg hover:bg-purple-700 transition-all duration-300 shadow-md hover:shadow-lg">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- News Stories Grid -->
        <div class="grid-3x3 mb-12">
            <?php foreach ($formatted_stories as $story): ?>
            <article class="news-card bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="relative h-48"> <!-- Adjusted height for better proportions -->
                    <img src="<?php echo htmlspecialchars($story['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($story['title']); ?>"
                         class="w-full h-full object-cover transition-transform duration-300"
                         onerror="this.src='https://placehold.co/800x600/6B46C1/FFFFFF/png?text=News&font=roboto'">
                    <div class="absolute bottom-0 left-0 right-0 p-4 image-overlay">
                        <?php $encoded_title = urlencode($story['title']); ?>
                        <span class="inline-block px-3 py-1 text-sm bg-purple-600 text-white rounded-full shadow-lg">
                            <?php echo htmlspecialchars($story['category']); ?>
                        </span>
                    </div>
                </div>
                <div class="news-card-content p-6">
                    <div class="news-card-body">
                        <div class="flex items-center mb-3">
                            <span class="text-sm font-medium text-purple-600">
                                <?php echo htmlspecialchars($story['source']); ?>
                            </span>
                            <span class="mx-2 text-gray-300">•</span>
                            <time class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($story['date']); ?>
                            </time>
                        </div>
                        <h2 class="text-xl font-bold mb-3 text-gray-800 line-clamp-2 hover:text-purple-600 transition-colors duration-300">
                            <a href="<?php echo htmlspecialchars($story['url']); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo htmlspecialchars($story['title']); ?>
                            </a>
                        </h2>
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            <?php echo htmlspecialchars($story['summary']); ?>
                        </p>
                    </div>
                    <div class="news-card-footer p-4">
                        <div class="flex justify-between items-center gap-4">
                            <a href="<?php echo htmlspecialchars($story['url']); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium transition-colors duration-300">
                                <span>Read More</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <a href="feedback.php?title=<?php echo urlencode($story['title']); ?>" 
                               class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm inline-flex items-center gap-2">
                                Submit Feedback
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-3">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-300 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Previous
            </a>
            <?php endif; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" 
               class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-300 flex items-center">
                Next
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
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
