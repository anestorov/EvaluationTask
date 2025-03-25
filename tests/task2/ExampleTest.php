<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    private $fileName;

    function test_assignment_example_1()
    {
        $fileName = 'data/task2/testExample1.json';
        @unlink($fileName);

        new Task2\AtomicWriter('Love grows where kindness lives.', $fileName);

        $reader = new Task2\AtomicReader($fileName);
        $data = $reader->getWords();

        //assert entire object match
        $shouldBe = [
            'love' => 1,
            'grows' => 1,
            'where' => 1,
            'kindness' => 1,
            'lives' => 1,
        ];
        $this->assertEquals($shouldBe, $data);

        @unlink($fileName);
    }

    function test_assignment_example_2()
    {
        $fileName = 'data/task2/testExample2.json';
        @unlink($fileName);

        new Task2\AtomicWriter('Love grows where kindness lives.', $fileName);

        new Task2\AtomicWriter('Kindness lives in every heart.', $fileName);

        $reader = new Task2\AtomicReader($fileName);
        $data = $reader->getWords();

        $shouldBe = [
            'love' => 1,
            'grows' => 1,
            'where' => 1,
            'kindness' => 2,
            'lives' => 2,
            'in' => 1,
            'every' => 1,
            'heart' => 1,
        ];
        $this->assertEquals($shouldBe, $data);

        @unlink($fileName);
    }

}
