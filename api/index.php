<?php

putenv("LARAVEL_STORAGE_PATH=/tmp");
$_ENV["LARAVEL_STORAGE_PATH"] = "/tmp";
$_ENV["APP_ENV"] = "production";
$_ENV["APP_DEBUG"] = "false";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token, Accept, Origin");

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit(0);
}

try {
    require __DIR__ . "/../vendor/autoload.php";
    $app = require_once __DIR__ . "/../bootstrap/app.php";

    // Bind Dummy View yang memenuhi Contract View & Factory Laravel
    $app->singleton("view", function() {
        return new class implements Illuminate\Contracts\View\Factory, Illuminate\Contracts\View\View {
            public function make($view = null, $data = [], $mergeData = []) { return $this; }
            public function file($path, $data = [], $mergeData = []) { return $this; }
            public function exists($view) { return false; }
            public function share($key, $value = null) { return $this; }
            public function render() { return ""; }
            public function with($key, $value = null) { return $this; }
            public function name() { return "dummy"; }
            public function getData() { return []; }
            public function offsetExists($offset): bool { return false; }
            public function offsetGet($offset): mixed { return null; }
            public function offsetSet($offset, $value): void {}
            public function offsetUnset($offset): void {}
            public function __call($method, $parameters) { return $this; }
        };
    });

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->headers->set("Access-Control-Allow-Origin", "*");
    $response->headers->set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS, PATCH");
    $response->headers->set("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Requested-With, X-CSRF-Token, Accept, Origin");

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
    exit(0);
}