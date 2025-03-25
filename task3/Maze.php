<?php

namespace Task3;

/**
 * Class Maze
 * @package Task3
 */
class Maze
{
    /** @var MazeNode[][] */
    private $map = [];

    /** @var MazeNode[] */
    private array $queue = [];

    private int $walls = 0;

    /** @var MazeNode[] */
    private array $itterations = [];

    private bool $isLoaded = false;
    private bool $isSolved = false;
    private bool $hasSolution = false;

    /**
     * Requires the number of walls that can be broken
     * @param int $numberOfWallsThatCanBeBroken
     */
    function __construct(int $numberOfWallsThatCanBeBroken)
    {
        $this->walls = $numberOfWallsThatCanBeBroken;
    }

    /**
     * Loads the map from a json string
     * @param string $map
     * @throws \Exception
     */
    function loadMapFromJSON(string $map): void
    {
        if ($this->isLoaded) throw new \Exception("Map is already loaded!");

        $mapArr = json_decode($map, true);

        if (!is_array($mapArr)) throw new \Exception("Invalid map");

        $this->map = $mapArr;

        if (!$this->validateMap()) throw new \Exception("Invalid map");

        $this->initializeNodes();
        $this->isLoaded = true;
    }

    /**
     * Loads the map from a json file
     * @param string $path
     * @throws \Exception
     */
    function loadMapFromFile(string $path): void
    {
        if (!file_exists($path)) throw new \Exception("File not found");

        $jsonString = @file_get_contents($path);

        if ($jsonString === false) throw new \Exception("Invalid file");

        $this->loadMapFromJSON($jsonString);
    }

    /**
     * Solves the maze
     * @return bool
     * @throws \Exception
     */
    function solve(): bool
    {
        if (!$this->isLoaded) {
            throw new \Exception("Map is not loaded! Please load the map first.");
        }

        if (empty($this->map) || empty($this->map[0])) {
            throw new \Exception("Map is empty!");
        }

        if ($this->hasSolution) return true;

        $rootNode = $this->map[0][0];
        $rootNode->setRoot();
        $this->queue[] = $rootNode;

        while (!empty($this->queue)) {
            $node = array_shift($this->queue);
            $this->itterations[] = clone $node;

            if ($node->isAt($this->mapEndCoordinates())) {
                $this->isSolved = true;
                $this->hasSolution = true;
                return true;
            }
            $this->visitNode($node);
        }
        $this->isSolved = true;
        $this->hasSolution = false;
        return false;
    }

    /**
     * Returns the shortest path of the maze
     * @return MazePath
     * @throws \Exception
     */
    function getSolution(): MazePath
    {
        if (!$this->isSolved) {
            throw new \Exception("Maze is not solved yet! Call solve() method first.");
        }
        if (!$this->hasSolution) {
            throw new \Exception("Maze has no solution!");
        }
        return $this->getEndNode()->getShortestPath()->addAndClone($this->getEndNode());
    }

    /**
     * Returns the itterations of the maze solving process
     * @return MazeNode[]
     * @throws \Exception
     */
    function getItterations(): array
    {
        return $this->itterations;
    }

    /**
     * Returns the map as a 2D array of MazeNode objects
     * @return MazeNode[][]
     * @throws \Exception
     */
    function getMap(): array
    {
        if (!$this->isLoaded) {
            throw new \Exception("Map is not loaded! Please load the map first.");
        }
        return $this->map;
    }


    /**
     * Visits the node from the queue and updates neighbour paths
     * @param MazeNode $node
     * @throws \Exception
     */
    private function visitNode(MazeNode $node): void
    {
        $neighbours = $this->getNeighbours($node);
        foreach ($neighbours as $neighbour) { // loop through all the neighbours
            $hasChange = false;

            if ($neighbour->isWall()) { // current neighbour is a wall

                $neighbour->setPath(0, new MazePath()); //Path 0 (no wall path) is discontinued;

                // loop through all non-wall crossing paths
                for ($pathInd = 1; $pathInd < $neighbour->pathsSize(); $pathInd++) {
                    if ($neighbour->getPath($pathInd)->length() > $node->getPath($pathInd - 1)->length() + 1) {

                        //if neighbor path through one more wall is longer than current node path + 1
                        //set neighbor path through one more wall to current node path + 1 and mark it as changed

                        $hasChange = true;
                        $neighbour->setPath($pathInd, $node->getPath($pathInd - 1)->addAndClone($node));
                    }
                }
            } else { // current neighbour is not a wall 

                // loop through all the paths
                for ($pathInd = 0; $pathInd < $neighbour->pathsSize(); $pathInd++) {
                    if ($neighbour->getPath($pathInd)->length() > $node->getPath($pathInd)->length() + 1) {

                        //if neighbor path is longer than current node path + 1
                        //set neighbor path to current node path + 1 and mark it as changed

                        $hasChange = true;
                        $neighbour->setPath($pathInd, $node->getPath($pathInd)->addAndClone($node));
                    }
                }
            }
            //if there was a change in the neighbor nodes, add it to the queue
            if ($hasChange) $this->queue[] = $neighbour;
        }
    }

    /**
     * Returns the neighbours of the given node
     * @param MazeNode $node
     * @return MazeNode[]
     */
    private function getNeighbours(MazeNode $node): array
    {
        $row = $node->getRow();
        $col = $node->getCol();
        $neighbours = [];

        if (isset($this->map[$row - 1][$col])) { // top
            $neighbours[] = $this->map[$row - 1][$col];
        }
        if (isset($this->map[$row][$col - 1])) { // left
            $neighbours[] = $this->map[$row][$col - 1];
        }
        if (isset($this->map[$row + 1][$col])) { // bottom
            $neighbours[] = $this->map[$row + 1][$col];
        }
        if (isset($this->map[$row][$col + 1])) { // right
            $neighbours[] = $this->map[$row][$col + 1];
        }

        return $neighbours;
    }

    /**
     * Returns the end node of the map
     * @return MazeNode
     * @throws \Exception
     */
    private function getEndNode(): MazeNode
    {
        $endNode = $this->map[count($this->map) - 1][count($this->map[0]) - 1];
        if (!isset($endNode) || !($endNode instanceof MazeNode)) {
            throw new \Exception("End node not found");
        }
        return $endNode;
    }

    /**
     * Check if the map is vaid square matrix and all the values are numeric
     * @return bool
     */
    private function validateMap(): bool
    {
        if (!is_array($this->map) || empty($this->map) || !is_array($this->map[0]) || empty($this->map[0])) {
            return false;
        }

        $rowSize = count($this->map);
        $colSize = count($this->map[0]);

        for ($i = 0; $i < $rowSize; $i++) {
            if (count($this->map[$i]) != $colSize) {
                return false;
                for ($j = 0; $j < $colSize; $j++) {
                    if (!is_numeric($this->map[$i][$j])) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Initialize all the nodes in the map to MazeNode objects
     */
    private function initializeNodes(): void
    {
        for ($row = 0; $row < count($this->map); $row++) {
            for ($col = 0; $col < count($this->map[$row]); $col++) {
                $this->map[$row][$col] = new MazeNode($row, $col, $this->map[$row][$col] == 1, $this->walls);
            }
        }
    }

    /**
     * Gets the end coordinates of the map in tuple form [row, col]
     * @return array
     */
    private function mapEndCoordinates(): array
    {
        return [count($this->map) - 1, count($this->map[0]) - 1];
    }
}
