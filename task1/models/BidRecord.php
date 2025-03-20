<?php

namespace Task1\models;

use Task1\interfaces\Model;


/**
 * This class represents a bid record
 * 
 * @package AdCash
 */
class BidRecord implements Model
{
    public int $id;
    public float $bid;

    function __construct() {}

    function setFromArray(array $data): void
    {
        if (is_numeric($data[0]) === false || is_numeric($data[1]) === false) {
            throw new \Exception("Invalid non-numeric data is supplied!");
        }
        $this->id = intval($data[0]);
        $this->bid = floatval($data[1]);
    }
}
