<?php
header("Content-Type: application/json");
echo json_encode([
    "view_provider_exists" => class_exists("Illuminate\\View\\ViewServiceProvider"),
    "vendor_view_folder" => is_dir(__DIR__ . "/../vendor/laravel/framework/src/Illuminate/View"),
    "providers_file" => file_exists(__DIR__ . "/../bootstrap/providers.php") ? file_get_contents(__DIR__ . "/../bootstrap/providers.php") : "MISSING",
]);