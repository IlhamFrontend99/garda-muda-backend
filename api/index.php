<?php
if (isset($_GET["debug"])) {
    header("Content-Type: application/json");
    echo json_encode([
        "view_provider_class_exists" => class_exists("Illuminate\\View\\ViewServiceProvider"),
        "vendor_view_folder_exists" => is_dir(__DIR__ . "/../vendor/laravel/framework/src/Illuminate/View"),
        "providers_file_content" => file_exists(__DIR__ . "/../bootstrap/providers.php") ? file_get_contents(__DIR__ . "/../bootstrap/providers.php") : "MISSING",
        "app_php_content" => file_exists(__DIR__ . "/../bootstrap/app.php") ? file_get_contents(__DIR__ . "/../bootstrap/app.php") : "MISSING",
        "composer_lock_exists" => file_exists(__DIR__ . "/../composer.lock"),
        "vendor_autoload_exists" => file_exists(__DIR__ . "/../vendor/autoload.php"),
    ]);
    exit(0);
}

putenv("LARAVEL_STORAGE_PATH=/tmp/storage");
$_ENV["LARAVEL_STORAGE_PATH"] = "/tmp/storage";
$_ENV["LOG_CHANNEL"] = "stderr";
$_ENV["CACHE_STORE"] = "array";
$_ENV["SESSION_DRIVER"] = "cookie";

@mkdir("/tmp/storage/logs", 0777, true);
@mkdir("/tmp/storage/framework/views", 0777, true);
@mkdir("/tmp/storage/framework/cache", 0777, true);
@mkdir("/tmp/storage/framework/sessions", 0777, true);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token, Accept, Origin");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") { http_response_code(200); exit(0); }

try {
    require __DIR__ . "/../vendor/autoload.php";
    $app = require_once __DIR__ . "/../bootstrap/app.php";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
    $response->headers->set("Access-Control-Allow-Origin", "*");
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    http_response_code(500);
    header("Content-Type: application/json");
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}