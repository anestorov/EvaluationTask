<?php

namespace Tests;

use Task1\BidFinder;

class testTask1 extends Test
{
    public function __construct() {
        $this->test_base_case();
        $this->test_subsequent_increase_in_secondary();
        $this->test_handling_escaped_vales();
    }

    public function test_base_case()
    {
        $finder  = new BidFinder('tests/data/task1/case1.csv');
        $solution = $finder->find();
        $this->assertTrue($solution->id == 4 && $solution->bid == 33);
    }

    public function test_subsequent_increase_in_secondary()
    {
        $finder  = new BidFinder('tests/data/task1/case2.csv');
        $solution = $finder->find();
        $this->assertTrue($solution->id == 4 && $solution->bid == 48.7);
    }

    public function test_handling_escaped_vales()
    {
        $finder  = new BidFinder('tests/data/task1/case3.csv');
        $solution = $finder->find();
        $this->assertTrue($solution->id == 4 && $solution->bid == 33);
    }
}

