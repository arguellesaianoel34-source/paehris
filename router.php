<?php
// Add CORS headers to allow requests from the Replit proxy
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly when they exist (PHP built-in server)
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// If the request looks like a static asset path but the file doesn't exist,
// return a plain 404 immediately — never let CodeIgniter handle it, as CI
// would redirect (relative) and create an infinite redirect loop.
$static_prefixes = ['/assets/', '/uploads/', '/css/', '/js/', '/img/', '/fonts/'];
foreach ($static_prefixes as $prefix) {
    if (strpos($uri, $prefix) === 0) {
        http_response_code(404);
        exit();
    }
}

// Route everything else through CodeIgniter
require_once __DIR__ . '/index.php';
