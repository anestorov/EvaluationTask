<?php

use PHPUnit\Framework\TestCase;

class ReaderUnitTest extends TestCase
{
    private $ref;
    private $fileName = 'data/task2/testReader.json';

    function setUp(): void
    {
        //$this->ref = new ReflectionClass(Task2\AtomicReader::class);
    }
    function tearDown(): void
    {
        @unlink($this->fileName);
    }

    function test_reader_load()
    {
        file_put_contents($this->fileName, json_encode(['word' => 1]));

        $obj = new Task2\AtomicReader($this->fileName);
        $data = $obj->getWords();

        $this->assertEquals(['word' => 1], $data);
    }

    function test_reader_getWordCount()
    {
        file_put_contents($this->fileName, json_encode(['word' => 1]));

        $obj = new Task2\AtomicReader($this->fileName);
        $data = $obj->getWordCount('word');

        $this->assertEquals(1, $data);
    }

    function test_reader_getWordCount_not_found()
    {
        file_put_contents($this->fileName, json_encode(['word' => 1]));

        $obj = new Task2\AtomicReader($this->fileName);
        $data = $obj->getWordCount('notfound');

        $this->assertEquals(0, $data);
    }

    function test_reader_getWords()
    {
        file_put_contents($this->fileName, json_encode(['word' => 1, 'word2' => 2]));

        $obj = new Task2\AtomicReader($this->fileName);
        $data = $obj->getWords();

        $this->assertEquals(['word' => 1, 'word2' => 2], $data);
    }

    function test_reader_getWords_empty()
    {
        file_put_contents($this->fileName, json_encode([]));

        $obj = new Task2\AtomicReader($this->fileName);
        $data = $obj->getWords();

        $this->assertEquals([], $data);
    }

    function test_reader_getWords_invalid_json()
    {
        file_put_contents($this->fileName, 'invalid json');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Could not decode JSON');

        $obj = new Task2\AtomicReader($this->fileName);
    }

    function test_reader_getWords_invalid_json_empty()
    {
        file_put_contents($this->fileName, '');

        $obj = new Task2\AtomicReader($this->fileName);
        $data = $obj->getWords();

        $this->assertEquals([], $data);
    }
}
