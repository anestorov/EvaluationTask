<?php

namespace Task3;

class MazePath implements \JsonSerializable
{
    /** @var MazeNode[] | null */
    private ?array $track = null;

    
    /**
     * @param bool $belongsToRoot If the path belongs to the root node
     */
    function __construct(bool $belongsToRoot = false)
    {
        if ($belongsToRoot) {
            $this->track = [];
        }
    }

    /**
     * Adds a node to the path
     * @param MazeNode $node
     * @return void
     */
    function add(MazeNode $node): void
    {
        if ($this->track === null) {
            $this->track = [];
        }
        $this->track[] = $node;
    }

    /**
     * Adds a node to the path and returns a clone of the path object
     * @param MazeNode $node
     * @return MazePath
     */
    function addAndClone(MazeNode $node): MazePath
    {
        $obj = clone $this;
        $obj->add($node);
        return $obj;
    }

    /**
     * Returns the length of the path
     * If the path is discontinued, it returns PHP_INT_MAX
     * @return int
     */
    function length(): int
    {
        if ($this->track === null) return PHP_INT_MAX;
        return count($this->track);
    }

    /**
     * Returns the path length as a number or null if the path is discontinued
     * This should be used for JSON serialization only
     * @return array
     */
    function toNumber(): int | null
    {
        if ($this->track === null) return null;
        return count($this->track) + 1;
    }

    /**
     * Required for json serialization
     */
    function jsonSerialize(): mixed
    {
        return array_map(function ($node) {
            return [$node->getRow(), $node->getCol()];
        }, $this->track);
    }

    /**
     * Required for cloning the object
     */
    function __clone()
    {
        $this->track = [...$this->track];
    }
}
