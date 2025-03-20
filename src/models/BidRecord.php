<?php

namespace AdCash\models;

use AdCash\interfaces\Model;

class BidRecord implements Model
{
    public int $id;
    public float $bid;

    function __construct() {}

    function setFromArray(array $data): void
    {
        $this->id = $data[0];
        $this->bid = $data[1];
    }
}
