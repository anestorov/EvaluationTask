<?php

declare(strict_types=1);

use Task3\Maze;

require_once "vendor/autoload.php";


$maze = new Maze(1);

$maze->loadMapFromFile("data/task3/case1.json");
if (!$maze->solve()) {
    echo "No path found";
    exit;
}

echo "Path Length: " . $maze->getSolution()->length() . PHP_EOL;
echo "Path: " . json_encode($maze->getSolution()) . PHP_EOL;
echo "Itterations: " . count($maze->getItterations()) . PHP_EOL;

// foreach ($maze->getItterations() as $node) {
//     echo $node->toJSON(true) . PHP_EOL;
// }


//echo json_encode($maze->getMap());