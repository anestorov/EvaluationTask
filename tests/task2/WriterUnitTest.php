<?php

use PHPUnit\Framework\TestCase;

class WriterUnitTest extends TestCase
{
    private $ref;
    private $fileName = 'data/task2/testWriter.json';

    function setUp(): void
    {
        $this->ref = new ReflectionClass(Task2\AtomicWriter::class);
    }
    function tearDown(): void
    {
        @unlink($this->fileName);
    }

    function test_getting_file_handle()
    {
        @unlink($this->fileName);

        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, $this->fileName);

        $this->ref->getMethod('getFileHandle')->invoke($obj);
        $fileHandle = $this->ref->getProperty('fileHandle')->getValue($obj);

        $this->assertIsResource($fileHandle);
        $this->assertNotSame(false, $fileHandle);

        @unlink($this->fileName);
    }

    function test_getting_invalid_file_handle()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, '/dev/null/invalid_file');

        $this->expectException(\Exception::class);
        $this->ref->getMethod('getFileHandle')->invoke($obj);
    }

    function test_creating_file()
    {
        @unlink($this->fileName);

        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, $this->fileName);

        $this->ref->getMethod('getFileHandle')->invoke($obj);
        $fileHandle = $this->ref->getProperty('fileHandle')->getValue($obj);

        $this->assertIsResource($fileHandle);
        $this->assertNotSame(false, $fileHandle);

        unset($obj);
        @unlink($this->fileName);
    }


    public function test_adding_word()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getMethod('addWord')->invokeArgs($obj, ['word']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word', $data);
        $this->assertEquals(1, $data['word']);
    }

    public function test_adding_existing_word()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getProperty('data')->setValue($obj, ['word' => 1]);
        $this->ref->getMethod('addWord')->invokeArgs($obj, ['word']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word', $data);
        $this->assertEquals(2, $data['word']);
    }

    public function test_adding_sentence()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getMethod('processWords')->invokeArgs($obj, ['word word']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word', $data);
        $this->assertEquals(2, $data['word']);
    }

    public function test_adding_sentence_with_spaces()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getMethod('processWords')->invokeArgs($obj, [' word word ']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word', $data);
        $this->assertEquals(2, $data['word']);
    }

    public function test_adding_sentence_with_uppercase()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getMethod('processWords')->invokeArgs($obj, ['WoRd wOrD']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word', $data);
        $this->assertEquals(2, $data['word']);
    }

    public function test_adding_sentence_with_special_chars()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getMethod('processWords')->invokeArgs($obj, ['word! word']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word', $data);
        $this->assertEquals(2, $data['word']);
    }

    public function test_adding_sentence_with_numbers()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getMethod('processWords')->invokeArgs($obj, ['word1 word']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word1', $data);
        $this->assertEquals(1, $data['word1']);
    }

    public function test_adding_sentence_with_mixed_chars()
    {
        $obj = $this->ref->newInstanceWithoutConstructor();

        $this->ref->getMethod('processWords')->invokeArgs($obj, ['word1! word']);
        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertArrayHasKey('word1', $data);
        $this->assertEquals(1, $data['word1']);
    }

    function test_writing_to_file()
    {
        file_put_contents($this->fileName, 'this is a test');

        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, $this->fileName);

        $this->ref->getMethod('getFileHandle')->invoke($obj);
        $this->ref->getProperty('data')->setValue($obj, ['word' => 1]);
        $this->ref->getMethod('saveToFile')->invoke($obj);

        $data = json_decode(file_get_contents($this->fileName), true);

        $this->assertEquals(['word' => 1], $data);

        @unlink($this->fileName);
    }

    function test_reading_from_file()
    {
        file_put_contents($this->fileName, json_encode(['word' => 1, 'word2' => 2]));

        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, $this->fileName);

        $this->ref->getMethod('getFileHandle')->invoke($obj);
        $this->ref->getMethod('setExclusiveLock')->invoke($obj);
        $this->ref->getMethod('loadFromFile')->invoke($obj);

        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertEquals(['word' => 1, 'word2' => 2], $data);

        unset($obj);
        @unlink($this->fileName);
    }

    function test_reading_from_empty_file()
    {
        file_put_contents($this->fileName, '');

        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, $this->fileName);

        $this->ref->getMethod('getFileHandle')->invoke($obj);
        $this->ref->getMethod('setExclusiveLock')->invoke($obj);
        $this->ref->getMethod('loadFromFile')->invoke($obj);

        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertEquals([], $data);

        unset($obj);
        @unlink($this->fileName);
    }

    function test_reading_from_file_and_adding_sentence()
    {
        file_put_contents($this->fileName, json_encode(['word' => 1, 'word2' => 2]));

        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, $this->fileName);

        $this->ref->getMethod('getFileHandle')->invoke($obj);
        $this->ref->getMethod('loadFromFile')->invoke($obj);
        $this->ref->getMethod('processWords')->invokeArgs($obj, ['peace word']);

        $data = $this->ref->getProperty('data')->getValue($obj);

        $this->assertEquals(['word' => 2, 'word2' => 2, 'peace' => 1], $data);

        unset($obj);
        @unlink($this->fileName);
    }

    function test_reading_file_adding_sentence_and_writing_to_file()
    {
        file_put_contents($this->fileName, json_encode(['word' => 1, 'word2' => 2]));

        $obj = $this->ref->newInstanceWithoutConstructor();
        $this->ref->getProperty('file')->setValue($obj, $this->fileName);

        $this->ref->getMethod('getFileHandle')->invoke($obj);
        $this->ref->getMethod('setExclusiveLock')->invoke($obj);
        $this->ref->getMethod('loadFromFile')->invoke($obj);
        $this->ref->getMethod('processWords')->invokeArgs($obj, ['peace word']);
        $this->ref->getMethod('saveToFile')->invoke($obj);
        $this->ref->getMethod('releaseLock')->invoke($obj);

        $data = json_decode(file_get_contents($this->fileName), true);

        $this->assertEquals(['word' => 2, 'word2' => 2, 'peace' => 1], $data);

        unset($obj);
        @unlink($this->fileName);
    }
}
