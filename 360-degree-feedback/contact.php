<?php
session_start();
require_once 'includes/auth.php';
require 'db.php';

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message_content = $conn->real_escape_string($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message_content);

    if ($stmt->execute()) {
        $message = "Thank you for your message! We'll get back to you soon.";
        $messageType = "success";
    } else {
        $message = "Sorry, there was an error sending your message.";
        $messageType = "error";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - 360° Feedback Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            ring: 2px;
            ring-color: #8b5cf6;
            border-color: #8b5cf6;
        }
        .map-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s ease forwards;
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Animations */
        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Apply animations to elements */
        .contact-form {
            opacity: 0;
            animation: scaleIn 0.6s ease forwards;
        }

        .contact-info-item {
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.6s ease forwards;
        }

        .contact-info-item:nth-child(1) { animation-delay: 0.2s; }
        .contact-info-item:nth-child(2) { animation-delay: 0.4s; }
        .contact-info-item:nth-child(3) { animation-delay: 0.6s; }

        /* Hover effects */
        .contact-form input,
        .contact-form textarea {
            transition: all 0.3s ease;
        }

        .contact-form input:hover,
        .contact-form textarea:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .contact-form button {
            transition: all 0.3s ease;
            background-size: 200% auto;
            background-image: linear-gradient(45deg, #6366f1 0%, #8b5cf6 50%, #6366f1 100%);
        }

        .contact-form button:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.3);
        }

        .info-card {
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .contact-icon {
            transition: all 0.3s ease;
        }

        .info-card:hover .contact-icon {
            transform: scale(1.1);
            color: #6366f1;
        }

        /* Success/Error message animation */
        .alert-message {
            animation: slideIn 0.5s ease forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-700 to-purple-900 text-white shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-xl font-bold">360° Feedback Portal</a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="hover:text-blue-200 transition">Home</a>
                    <a href="about.html" class="hover:text-blue-200 transition">About</a>
                    <a href="news.php" class="hover:text-blue-200 transition">News Stories</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="hover:text-blue-200 transition">Dashboard</a>
                        <a href="logout.php" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors duration-300">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition-colors duration-300">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contact Section -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <?php if ($message): ?>
                <div class="alert-message mb-8 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white p-8 rounded-lg shadow-lg contact-form">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Get in Touch</h2>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" id="subject" name="subject" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea id="message" name="message" rows="5" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-purple-600 to-purple-800 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-purple-900 transition-all duration-300 transform hover:scale-105">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-8">
                    <div class="bg-white p-8 rounded-lg shadow-lg info-card">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Contact Information</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4 contact-info-item">
                                <svg class="w-6 h-6 text-purple-600 mt-1 contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-700">Address</h4>
                                    <p class="text-gray-600">Lovely Professional University<br>Jalandhar - Delhi G.T. Road<br>Phagwara, Punjab 144411<br>India</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4 contact-info-item">
                                <svg class="w-6 h-6 text-purple-600 mt-1 contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-700">Email</h4>
                                    <a href="mailto:info@lpu.co.in" class="text-purple-600 hover:text-purple-800">info@lpu.co.in</a>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4 contact-info-item">
                                <svg class="w-6 h-6 text-purple-600 mt-1 contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <div>
                                    <h4 class="font-semibold text-gray-700">Phone</h4>
                                    <p class="text-gray-600">+91 1824 404404</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Location</h3>
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3407.139010183434!2d75.70397477534183!3d31.253811974421252!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391a5f5e9c489cf3%3A0x4049a5409d53c300!2sLovely%20Professional%20University!5e0!3m2!1sen!2sin!4v1682854532084!5m2!1sen!2sin" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center text-gray-400">
                <p>&copy; 2024 360° Feedback Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
