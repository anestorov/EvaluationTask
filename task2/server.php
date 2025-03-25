<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Task2\AtomicReader;
use Task2\AtomicWriter;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $reader = new AtomicReader();

    if (isset($_GET['word'])) {
        $data = $reader->getWordCount($_GET['word']);
        echo json_encode(['word' => $_GET['word'], 'count' => $data]);
        return;
    }
    $data = $reader->getWords();
    echo json_encode($data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['data'])) {
            $writer = new AtomicWriter($_POST['data']);
            echo json_encode(['status' => 'success']);
            return;
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
        return;
    }
}
