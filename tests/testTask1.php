<?php

namespace Tests;

use AdCash\BidFinder;

class testTask1 extends Test
{
    public function __construct() {
        $this->baseCase();
        $this->shouldFail();
    }

    public function baseCase()
    {
        $finder  = new BidFinder('tests/data/task1/case1.csv');
        $solution = $finder->find();
        $this->assertTrue($solution->id == 4 && $solution->bid == 33);
    }

    public function shouldFail()
    {
        $finder  = new BidFinder('tests/data/task1/case1.csv');
        $solution = $finder->find();
        $this->assertTrue($solution->id == 4 && $solution->bid == 32);
    }
}

