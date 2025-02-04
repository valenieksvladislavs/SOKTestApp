<?php
require __DIR__ . '/vendor/autoload.php';

use App\Renderer;
use App\Router;

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'db';
$db   = 'soktestapp';
$user = 'soktestapp';
$pass = 'soktestapp';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

$renderer = new Renderer();

$router = new Router($_SERVER['REQUEST_URI'], $pdo, $renderer);

try {
  echo $router->dispatch();
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['errors' => [['key' => 'system', 'message' => $e->getMessage()]]]);
}
