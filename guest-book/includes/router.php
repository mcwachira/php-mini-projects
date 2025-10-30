<?php
declare(strict_types=1);

/**
 * Allowed HTTP methods
 */
const ALLOWED_METHODS = ['GET', 'POST'];

/**
 * When the URI is empty ("/"), treat it as "index"
 */
const INDEX_URI = '';
const INDEX_ROUTE = 'index';

/**
 * Normalizes the URI:
 * - Removes query strings
 * - Removes the base folder (e.g. "/guest-book/public")
 * - Converts to lowercase
 * - Removes leading/trailing slashes
 * - Converts "" to "index"
 */
function normalizeUri(string $uri): string {

    $uri = strtok($uri, '?');
    // Remove query string and keep only path
    $uri = parse_url($uri, PHP_URL_PATH);

    // Convert to lowercase
    $uri = strtolower($uri);

    // Detect base path (e.g. "/guest-book/public")
    $basePath = dirname($_SERVER['SCRIPT_NAME']);

    // Remove base path from URI
    if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
        $uri = substr($uri, strlen($basePath));
    }

    // Remove leading/trailing slashes
    $uri = trim($uri, '/');

    // Convert empty string → "index"
    return $uri === '' ? INDEX_ROUTE : $uri;
}

/**
 * Builds the full path to the route file.
 */
function getFilePath(string $uri, string $method): string {
    return ROUTES_DIR . '/' . $uri . '_' . strtolower($method) . '.php';
}

/**
 * Sends a 404 error message and stops execution
 */
function notFound(): void {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

function badRequest(string $message = 'Bad Request'): void {
    http_response_code(400);
    echo $message;
    exit;
}

function serverError(string $message = 'Server Error'):void{
    http_response_code(500);
    echo $message;
    exit;

}

function redirect(string $uri): void {
    header("Location: $uri");
    exit();
}
/**
 * Main router function.
 * Decides which route file to load based on URI + method
 */
function dispatch(string $uri, string $method) {

    // Normalize URI (remove base path, lowercase, "/ → index")
    $uri = normalizeUri($uri);

    // Normalize HTTP method (GET, POST)
    $method = strtoupper($method);

    // DEBUG OUTPUT — remove when finished testing
    echo "<pre style='background:#222;color:#0f0;padding:10px'>";
    echo "=== DEBUG ROUTER ===\n";
    echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
    echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
    echo "BASE PATH: " . dirname($_SERVER['SCRIPT_NAME']) . "\n";
    echo "Normalized URI: " . $uri . "\n";
    echo "HTTP Method: " . $method . "\n";
    echo "ROUTE FILE CHECK:\n";
    echo getFilePath($uri, $method) . "\n";
    echo "File exists? " . (file_exists(getFilePath($uri, $method)) ? "YES ✅" : "NO ❌") . "\n";
    echo "</pre>";
    // END DEBUG

    // If method not supported → 404
    if (!in_array($method, ALLOWED_METHODS)) {
        notFound();
    }

    // Try loading the route
    $filePath = getFilePath($uri, $method);

    if (file_exists($filePath)) {
        include $filePath;
        return;
    }

    // Route not found → 404
    notFound();
}
