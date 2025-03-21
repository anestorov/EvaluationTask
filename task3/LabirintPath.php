<?php

namespace Task3;

class LabirintPath
{
    function __construct(private array $track = []) {}

    public function clone()
    {
        return new LabirintPath(json_decode(json_encode($this->track), true));
    }

    function addStep($row, $col)
    {
        $this->track["R:$row|C:$col"] = [$row, $col];
    }

    function isVisited($row, $col)
    {
        return isset($this->track["R:$row|C:$col"]);
    }

    function length()
    {
        return count($this->track);
    }

    function show()
    {
        return array_keys($this->track);
    }
}
