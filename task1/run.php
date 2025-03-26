<?php

declare(strict_types=1);
require_once __DIR__."/../vendor/autoload.php";

use Task1\BidFinder;

if ($argc < 2) {
    echo "Please provide the file name as an argument\n\n";
    echo "Usage: php run.php <file_name>\n";
    exit(1);
}

if($argv[1] === '--help') {
    echo "Usage: php run.php <file_name>\n";
    exit(0);
}

$startTime = microtime(true);  

$finder = new BidFinder($argv[1]);
$solution = $finder->find();
echo "Result ID: $solution->id Bid: $solution->bid \n\n";

$endTime = microtime(true);
echo "Execution time: " . round($endTime - $startTime,5) . " seconds\n";
echo 'Memory usage: ' . memory_get_peak_usage(true) / 1024 . " Kb\n";
