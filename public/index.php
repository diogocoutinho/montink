<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Start session
session_start();

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get router instance
$router = require_once __DIR__ . '/../app/routes.php';

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
