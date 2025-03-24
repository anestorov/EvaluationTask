<?php
require_once "../vendor/autoload.php";

use Task3\Maze;

if (@$_GET['action'] == 'getMap') {
    $map = file_get_contents("../data/task3/" . $_GET['map']);
    die($map);
}
if ($_POST['action'] == 'saveMap') {
    $map = $_POST['map'];
    $mapName = $_POST['mapName'];
    file_put_contents("../data/task3/" . $mapName . ".json", $map);
    die(json_encode([
        "status" => "success"
    ]));
}
if (@$_POST['action'] == 'solveMaze') {

    $maze = new Maze($_POST['passWalNumber']);
    $maze->loadMapFromJSON($_POST['map']);
    $hasSolution = $maze->solve();

    echo json_encode([
        "hasSolution" => $hasSolution,
        "solution" => $hasSolution ? $maze->getSolution() : [],
        "itterations" => $maze->getItterations(),
        "map" => $maze->getMap()
    ]);
}
