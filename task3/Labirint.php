<?php

namespace Task3;

class Labirint
{
    private $map = [];
    private $mapRows = 0;
    private $mapCols = 0;
    private $visited = [];

    function __construct(string $path)
    {
        $this->map = json_decode(file_get_contents($path), true);
        $this->mapRows = count($this->map);
        $this->mapCols = count($this->map[0]);

        $path = $this->explore(0, 0, new LabirintPath(), true, true);
        var_dump($path->show());
    }


    function explore(int $row, int $col, LabirintPath $myPath, bool $hasBrokenWall, bool $debug): LabirintPath | false
    {
        if ($debug) echo "Exploring $row, $col : ";

        if ($row == $this->mapRows - 1 && $col == $this->mapCols - 1) {
            if ($debug) echo "Exit Found! \n";
            $path = new LabirintPath();
            $path->addStep($row, $col);
            return $path;
        }
        // if (isset($this->visited[$row][$col][$hasBrokenWall])) {
        //     echo "Memoized! \n";
        //     var_dump($this->visited[$row][$col][$hasBrokenWall]);
        //     if ($this->visited[$row][$col][$hasBrokenWall] === false) return false;
        //     else return $this->visited[$row][$col][$hasBrokenWall]->clone();
        // }

        // if (!isset($this->map[$row][$col])) {
        //     if ($debug) echo "Beyond! \n";
        //     return false;
        // }

        if ($this->map[$row][$col] == 1) {
            if ($hasBrokenWall) {
                if ($debug) echo "2nd WALL! \n";
                return false;
            } else {
                $hasBrokenWall = true;
                if ($debug) echo " You can Broke one wall! ";
            }
        }



        if ($myPath->isVisited($row, $col)) {
            if ($debug) echo "Visited! \n";
            return false;
        }

        if ($debug) echo "going forward \n";
        $myPath->addStep($row, $col);
        $paths = [];

        // move right
        if ($this->inMap($row, $col + 1)) {
            $res = $this->explore($row, $col + 1, $myPath->clone(), $hasBrokenWall, $debug);
            //$this->visited[$row][$col + 1][$hasBrokenWall] = $res === false ? false : $res->clone();
            $paths[] = $res;
        }

        // move down
        if ($this->inMap($row + 1, $col)) {
            $res = $this->explore($row + 1, $col, $myPath->clone(), $hasBrokenWall, $debug);
            //$this->visited[$row + 1][$col][$hasBrokenWall] = $res === false ? false : $res->clone();
            $paths[] = $res;
        }

        // move left
        if ($this->inMap($row, $col - 1)) {
            $res = $this->explore($row, $col - 1, $myPath->clone(), $hasBrokenWall, $debug);
            //$this->visited[$row][$col - 1][$hasBrokenWall] = $res === false ? false : $res->clone();
            $paths[] = $res;
        }

        // move up
        if ($this->inMap($row - 1, $col)) {
            $res = $this->explore($row - 1, $col, $myPath->clone(), $hasBrokenWall, $debug);
            //$this->visited[$row - 1][$col][$hasBrokenWall] = $res === false ? false : $res->clone();
            $paths[] = $res;
        }

        $minPathLength = PHP_INT_MAX;
        $minPath = null;

        foreach ($paths as $path) {
            if ($path === false) continue;
            if ($path->length() < $minPathLength) {
                $minPathLength = $path->length();
                $minPath = $path;
            }
        }

        $res = false;
        if ($minPath === null) $res = false;
        else {
            $minPath->addStep($row, $col);
            $res = $minPath;
        }
        $this->visited[$row][$col][$hasBrokenWall] = $res === false ? false : ($res->clone());
        return $res;
    }

    function inMap($row, $col)
    {
        return isset($this->map[$row][$col]);
    }
}
