<?php

use PHPUnit\Framework\TestCase;
use Task3\Maze;

class MazeSolutionTest extends TestCase
{
    function test_should_throw_exception_when_not_solved()
    {
        $maze = new Maze(1);

        $this->expectException(\Exception::class);

        $maze->getSolution();
    }
    function test_labirinth_case_1_throught_1_wall()
    {
        $maze = new Maze(1);
        $maze->loadMapFromFile('data/task3/case1.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(11, $solLength);
    }

    function test_labirinth_case_1_throught_0_wall()
    {
        $maze = new Maze(0);
        $maze->loadMapFromFile('data/task3/case1.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(21, $solLength);
    }

    function test_labirinth_case_2_throught_0_wall()
    {
        $maze = new Maze(0);
        $maze->loadMapFromFile('data/task3/case2.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(7, $solLength);
    }

    function test_labirinth_case_2_throught_1_wall()
    {
        $maze = new Maze(0);
        $maze->loadMapFromFile('data/task3/case2.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(7, $solLength);
    }

    function test_labirinth_case_3_throught_0_wall()
    {
        $maze = new Maze(0);
        $maze->loadMapFromFile('data/task3/case3.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(39, $solLength);
    }

    function test_labirinth_case_3_throught_1_wall()
    {
        $maze = new Maze(1);
        $maze->loadMapFromFile('data/task3/case3.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(39, $solLength);
    }

    function test_labirinth_case_4_throught_0_wall()
    {
        $maze = new Maze(0);
        $maze->loadMapFromFile('data/task3/case4.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(19, $solLength);
    }

    function test_labirinth_case_4_throught_1_wall()
    {
        $maze = new Maze(1);
        $maze->loadMapFromFile('data/task3/case4.json');
        $maze->solve();

        $solLength = $maze->getSolution()->length();

        $this->assertEquals(19, $solLength);
    }
}
