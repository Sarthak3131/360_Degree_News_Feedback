<?php
session_start();
session_unset();
session_destroy();
header("refresh:2; url=index.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
    <meta charset="UTF-8">
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-700">Logging out...</h1>
        <p class="text-gray-500 mt-2">You will be redirected to the homepage shortly.</p>
    </div>
</body>
</html>
