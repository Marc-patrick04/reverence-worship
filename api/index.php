<?php

// Load Laravel's public index
$publicPath = __DIR__ . '/../public';
if (!is_dir($publicPath)) {
    // Fallback for Vercel's file structure
    $publicPath = __DIR__ . '/public';
}

require $publicPath . '/index.php';