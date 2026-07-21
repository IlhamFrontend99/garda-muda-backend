<?php
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