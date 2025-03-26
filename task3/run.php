<?php

declare(strict_types=1);

use Task3\Maze;

require_once __DIR__ . "/../vendor/autoload.php";

if ($argc < 2) {
    echo "Please provide a file name";
    exit;
}

$uri = __DIR__ . "/../data/task3/" . $argv[1];
if (!file_exists($uri)) {
    echo "File not found";
    exit;
}
$maze = new Maze($argc > 2 ? $argv[2] : 1);

$maze->loadMapFromFile($uri);
if (!$maze->solve()) {
    echo "No path found";
    exit;
}

echo "Path Length: " . $maze->getSolution()->length() . PHP_EOL;
echo "Path: " . json_encode($maze->getSolution()) . PHP_EOL;
