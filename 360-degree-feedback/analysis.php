<?php
require 'db.php';
require_once 'includes/auth.php';
session_start();

// Check if user is logged in
requireLogin();

// Initialize default values for stats
$stats = [
    'total_feedback' => 0,
    'avg_impact' => 0,
    'avg_accuracy' => 0,
    'avg_clarity' => 0
];

// Fetch summary statistics
$stats_query = "SELECT 
    COUNT(*) as total_feedback,
    COALESCE(AVG(impact_rating), 0) as avg_impact,
    COALESCE(AVG(accuracy_rating), 0) as avg_accuracy,
    COALESCE(AVG(clarity_rating), 0) as avg_clarity
FROM article_feedback";
$stats_result = $conn->query($stats_query);
if ($stats_result) {
    $stats = $stats_result->fetch_assoc();
}

// Fetch trend data (last 7 days)
$trend_query = "SELECT 
    DATE(created_at) as date,
    AVG(impact_rating) as impact,
    AVG(accuracy_rating) as accuracy,
    AVG(clarity_rating) as clarity,
    COUNT(*) as count
FROM article_feedback
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY date ASC";
$trend_result = $conn->query($trend_query);

$trend_data = [
    'dates' => [],
    'impact' => [],
    'accuracy' => [],
    'clarity' => [],
    'counts' => []
];

while ($row = $trend_result->fetch_assoc()) {
    $trend_data['dates'][] = date('M d', strtotime($row['date']));
    $trend_data['impact'][] = round($row['impact'], 1);
    $trend_data['accuracy'][] = round($row['accuracy'], 1);
    $trend_data['clarity'][] = round($row['clarity'], 1);
    $trend_data['counts'][] = $row['count'];
}

// Fetch all feedback entries with article titles
$feedback_query = "SELECT f.*, a.title as article_title 
                  FROM article_feedback f 
                  JOIN articles a ON f.article_id = a.id 
                  ORDER BY f.created_at DESC";
$feedback_result = $conn->query($feedback_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Analysis - 360° Feedback Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-700 to-purple-900 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="text-xl font-bold">360° Feedback Portal</div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="hover:text-blue-200 transition">Home</a>
                    <a href="news.php" class="hover:text-blue-200 transition">News Stories</a>
                    <a href="analysis.php" class="hover:text-blue-200 transition">Analysis</a>
                    <a href="about.html" class="hover:text-blue-200 transition">About</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Feedback Analysis Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Feedback</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_feedback']; ?></p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-comments text-purple-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg. Impact Rating</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['avg_impact'], 1); ?></p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg. Accuracy Rating</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['avg_accuracy'], 1); ?></p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg. Clarity Rating</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['avg_clarity'], 1); ?></p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-lightbulb text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Rating Distribution</h2>
                <canvas id="ratingChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Rating Trends (Last 7 Days)</h2>
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Feedback List Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Recent Feedback</h2>
                <div class="flex gap-4">
                    <input type="text" id="searchInput" placeholder="Search feedback..." 
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <select id="sortSelect" 
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="impact">Highest Impact</option>
                        <option value="accuracy">Highest Accuracy</option>
                        <option value="clarity">Highest Clarity</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ratings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($row = $feedback_result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-normal">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['article_title']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm text-gray-900">
                                        Impact: <?php echo $row['impact_rating']; ?>/5
                                    </span>
                                    <span class="text-sm text-gray-900">
                                        Accuracy: <?php echo $row['accuracy_rating']; ?>/5
                                    </span>
                                    <span class="text-sm text-gray-900">
                                        Clarity: <?php echo $row['clarity_rating']; ?>/5
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($row['feedback']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Rating Distribution Chart
        const ratingCtx = document.getElementById('ratingChart').getContext('2d');
        new Chart(ratingCtx, {
            type: 'bar',
            data: {
                labels: ['Impact', 'Accuracy', 'Clarity'],
                datasets: [{
                    label: 'Average Ratings',
                    data: [
                        <?php echo $stats['avg_impact']; ?>,
                        <?php echo $stats['avg_accuracy']; ?>,
                        <?php echo $stats['avg_clarity']; ?>
                    ],
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.5)',
                        'rgba(34, 197, 94, 0.5)',
                        'rgba(234, 179, 8, 0.5)'
                    ],
                    borderColor: [
                        'rgb(99, 102, 241)',
                        'rgb(34, 197, 94)',
                        'rgb(234, 179, 8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });

        // Rating Trends Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($trend_data['dates']); ?>,
                datasets: [
                    {
                        label: 'Impact',
                        data: <?php echo json_encode($trend_data['impact']); ?>,
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Accuracy',
                        data: <?php echo json_encode($trend_data['accuracy']); ?>,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Clarity',
                        data: <?php echo json_encode($trend_data['clarity']); ?>,
                        borderColor: 'rgb(234, 179, 8)',
                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        title: {
                            display: true,
                            text: 'Rating'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            afterBody: function(context) {
                                const index = context[0].dataIndex;
                                return `Total Feedback: ${<?php echo json_encode($trend_data['counts']); ?>[index]}`;
                            }
                        }
                    }
                }
            }
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const sortSelect = document.getElementById('sortSelect');
        const tableBody = document.querySelector('tbody');
        const originalRows = Array.from(tableBody.querySelectorAll('tr'));

        function filterAndSortTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const sortValue = sortSelect.value;

            let filteredRows = originalRows.filter(row => {
                const text = row.textContent.toLowerCase();
                return text.includes(searchTerm);
            });

            // Sort rows based on selected option
            filteredRows.sort((a, b) => {
                if (sortValue === 'newest') {
                    return new Date(b.lastElementChild.textContent) - new Date(a.lastElementChild.textContent);
                } else if (sortValue === 'oldest') {
                    return new Date(a.lastElementChild.textContent) - new Date(b.lastElementChild.textContent);
                } else {
                    // Handle rating-based sorting
                    const getRating = (row, type) => {
                        const ratings = row.querySelector('td:nth-child(2)').textContent;
                        const match = ratings.match(new RegExp(`${type}: (\\d)/5`));
                        return match ? parseInt(match[1]) : 0;
                    };

                    if (sortValue === 'impact') {
                        return getRating(b, 'Impact') - getRating(a, 'Impact');
                    } else if (sortValue === 'accuracy') {
                        return getRating(b, 'Accuracy') - getRating(a, 'Accuracy');
                    } else if (sortValue === 'clarity') {
                        return getRating(b, 'Clarity') - getRating(a, 'Clarity');
                    }
                }
            });

            // Update table
            tableBody.innerHTML = '';
            filteredRows.forEach(row => tableBody.appendChild(row.cloneNode(true)));
        }

        searchInput.addEventListener('input', filterAndSortTable);
        sortSelect.addEventListener('change', filterAndSortTable);
    </script>
</body>
</html>
