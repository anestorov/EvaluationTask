<?php
declare(strict_types=1);
require_once "vendor/autoload.php";

use AdCash\BidFinder;

$finder = new BidFinder($argv[1]);
$solution = $finder->find();
echo "Result: $solution->id $solution->bid \n";
