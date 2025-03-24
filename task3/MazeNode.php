<?php

namespace Task3;

class MazeNode implements \JsonSerializable
{
    private int $row;
    private int $col;
    private bool $isWall = false;

    /** @var MazePath[] */
    private array $paths = [];

    /**
     * MazeNode constructor.
     * @param int $row
     * @param int $col
     * @param bool $isWall
     * @param int $numPaths
     */
    function __construct(int $row, int $col, bool $isWall, int $numPaths)
    {
        $this->row = $row;
        $this->col = $col;
        $this->isWall = $isWall;

        for ($i = 0; $i <= $numPaths; $i++) {
            $this->paths[$i] = new MazePath();
        }
    }

    /**
     * Returns the row of the node in the maze
     * @return int
     */
    function getRow(): int
    {
        return $this->row;
    }

    /**
     * Returns the column of the node in the maze
     * @return int
     */
    function getCol(): int
    {
        return $this->col;
    }

    /**
     * Returns if the node is a wall
     * @return bool
     */
    function isWall(): bool
    {
        return $this->isWall;
    }

    /**
     * Returns the number of paths to the node
     * @return int
     */
    function pathsSize(): int
    {
        return count($this->paths);
    }

    /**
     * Returns the paths of the node
     * @return MazePath[]
     */
    function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Returns the path at the given index
     * @param int $index
     * @return MazePath
     */
    function getPath(int $index): MazePath
    {
        return $this->paths[$index];
    }

    /**
     * Sets the path at the given index
     * @param int $index
     * @param MazePath $path
     * @return void
     */
    function setPath(int $index, MazePath $path): void
    {
        $this->paths[$index] = $path;
    }

    /**
     * Sets the node as a root node
     * @return void
     */
    function setRoot()
    {
        foreach ($this->paths as &$pathType) {
            $pathType = new MazePath(belongsToRoot: true);
        }
    }

    /**
     * Checks if the node is at the given row and column tuple
     * @param array $rowcol [row, col]
     * @return bool
     */
    function isAt(array $rowCol): bool
    {
        return $this->row == $rowCol[0] && $this->col == $rowCol[1];
    }

    
    /**
     * Returns the shortest path type to the node
     * @return MazePath
     */
    function getShortestPath(): MazePath
    {
        $shortestPath = null;
        $shortestLength = PHP_INT_MAX;

        foreach ($this->paths as $path) {
            if ($path->length() < $shortestLength) {
                $shortestLength = $path->length();
                $shortestPath = $path;
            }
        }

        return $shortestPath;
    }

    /** 
     * Required for cloning the object
     */
    function __clone()
    {
        $this->paths = [...$this->paths];
    }

    /**
     * Required for json encoding
     * @return array
     */
    function jsonSerialize(): array
    {
        return [
            "row" => $this->row,
            "col" => $this->col,
            "isWall" => $this->isWall,
            "paths" => array_map(function ($path) {
                return $path->toNumber();
            }, $this->paths)
        ];
    }
}
