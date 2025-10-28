<?php
declare(strict_types=1);

const ALLOWED_METHODS = ['GET', 'POST'];
const INDEX_URI = '';
const INDEX_ROUTE = 'index';

function normalizeUri(string $uri): string{
    $uri = parse_url($uri, PHP_URL_PATH);
    $uri = strtolower($uri);

    // Determine the base directory (e.g. "/guest-book/public")
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptName !== '/' && str_starts_with($uri, $scriptName)) {
        $uri = substr($uri, strlen($scriptName));
    }

    // Trim slashes
    $uri = trim($uri, '/');

    return $uri === INDEX_URI ? INDEX_ROUTE : $uri;
}

function getFilePath(string $uri, string $method): string {
    return ROUTES_DIR . '/' . normalizeUri($uri) . '_' . strtolower($method) . '.php';
}
function notFound():void{
    http_response_code(404);
    echo '404 Not Found';
    exit;
}
function dispatch(string $uri, string $method)
{

    //1. normalize the URI: GET /guestbook -> routes/guestbook_get.php
    $uri = normalizeUri($uri);
    $method = strtoupper($method);

//    echo "<pre>";
//    echo "URI: $uri\n";
//    echo "Method: $method\n";
//    echo "File path: " . getFilePath($uri, $method) . "\n";
//    echo "</pre>";

    //2. GET|POST - return 404
    if(!in_array($method, ALLOWED_METHODS)){
notFound();
    }

    //3. file path -PHP file path

   $filePath = getFilePath($uri, $method);

    if(file_exists($filePath)){
        include $filePath;
        return;
    }

    notFound();
    //4. if this file exist , if not 404
    //5. Handle the route by including the PHP file

}