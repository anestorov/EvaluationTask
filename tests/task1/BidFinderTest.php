<?php

use PHPUnit\Framework\TestCase;

class BidFinderTest extends TestCase
{

    function test_case_1()
    {

        $bidFinder = new Task1\BidFinder('case1.csv');
        $solution = $bidFinder->find();

        $this->assertEquals(4, $solution->id);
        $this->assertEquals(33, $solution->bid);
    }

    function test_case_2()
    {

        $bidFinder = new Task1\BidFinder('case2.csv');
        $solution = $bidFinder->find();

        $this->assertEquals(4, $solution->id);
        $this->assertEquals(48.7, $solution->bid);
    }

    function test_case_3()
    {

        $bidFinder = new Task1\BidFinder('case3.csv');
        $solution = $bidFinder->find();

        $this->assertEquals(5, $solution->id);
        $this->assertEquals(33.51, $solution->bid);
    }

    function test_1_million_records_csv()
    {

        $bidFinder = new Task1\BidFinder('1m.csv');
        $solution = $bidFinder->find();

        $this->assertEquals(131781, $solution->id);
        $this->assertEquals(999998.19, $solution->bid);
    }
}
