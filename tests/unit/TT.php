<?php

use PHPUnit\Framework\TestCase;

class Task1Test extends TestCase
{

    public function testMazeNode()
    {
        $mazeNode = new Task3\MazeNode(1, 1, false, 1);
        $this->assertEquals(1, $mazeNode->getRow());
        $this->assertEquals(1, $mazeNode->getCol());
        $this->assertEquals(false, $mazeNode->isWall());
    }

    public function testMaze1() {
        $maze = new Task3\Maze(1);
        $maze->loadMapFromFile("data/task3/case1.json");
        $this->assertTrue($maze->solve());
        $this->assertEquals(11, $maze->getSolution()->length());
    }

    public function testMaze2() {
        $maze = new Task3\Maze(0);
        $maze->loadMapFromFile("data/task3/case1.json");
        $this->assertTrue($maze->solve());
        $this->assertEquals(21, $maze->getSolution()->length());
    }

    public function testMaze3() {
        $maze = new Task3\Maze(0);
        $this->expectException(\Exception::class);

        $maze->loadMapFromFile("data/task3/nonexistantfile.json");

    }

    public function testMaze4() {
        $maze = new Task3\Maze(0);
        $this->expectException(\Exception::class);
        $maze->loadMapFromJSON('[]');

        // $this->expectException(\Exception::class);
        // $maze->loadMapFromJSON('{}');
    }

    // public function testStorage() {
    //     $storage = new Task2\Storage();
    //     $this->assertEquals('[]', $storage->getDataAsJson());
    //     $storage->add('word');
    //     $this->assertEquals('["word"]', $storage->getDataAsJson());
    // }
}
