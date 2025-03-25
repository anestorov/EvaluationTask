<?php

use PHPUnit\Framework\TestCase;

class MutexTest extends TestCase
{
    public function test_writer_file_lock_and_unlock()
    {
        $fileName = 'data/task2/testMutex.json';        

        $ref = new ReflectionClass(Task2\AtomicWriter::class);
        $obj = $ref->newInstanceWithoutConstructor();

        $ref->getProperty('file')->setValue($obj, $fileName);

        $ref->getMethod('getFileHandle')->invoke($obj);
        $ref->getMethod('setExclusiveLock')->invoke($obj);

        // assert can obtain non-blocking lock while file is locked

        $h = fopen($fileName, 'a+');
        $lockedForReading = flock($h, LOCK_SH|LOCK_NB);
        $lockedForWriting = flock($h, LOCK_EX|LOCK_NB);

        $this->assertFalse($lockedForReading);
        $this->assertFalse($lockedForWriting);

        $ref->getMethod('releaseLock')->invoke($obj);

        // assert can obtain non-blocking lock when file is unlocked

        $lockedForReading = flock($h, LOCK_SH|LOCK_NB);
        $lockedForWriting = flock($h, LOCK_EX|LOCK_NB);

        $this->assertTrue($lockedForReading);
        $this->assertTrue($lockedForWriting);

        unset($obj);
        @flock($h, LOCK_UN);
        @fclose($h);
        @unlink($fileName);
    }

    public function test_reader_file_lock_and_unlock()
    {
        $fileName = 'data/task2/testMutex.json';    
        file_put_contents($fileName, json_encode(['word' => 1]));    

        $ref = new ReflectionClass(Task2\AtomicReader::class);
        $obj = $ref->newInstanceWithoutConstructor();

        $ref->getProperty('file')->setValue($obj, $fileName);

        $ref->getMethod('getFileHandle')->invoke($obj);
        $ref->getMethod('setSharedLock')->invoke($obj);

        // assert can obtain non-blocking lock while file is locked for reading

        $h = fopen($fileName, 'a+');
        $lockedForReading = flock($h, LOCK_SH|LOCK_NB);
        $lockedForWriting = flock($h, LOCK_EX|LOCK_NB);

        $this->assertTrue($lockedForReading); // should be able to read while file is locked
        $this->assertFalse($lockedForWriting); // should not be able to write while file is locked

        $ref->getMethod('releaseLock')->invoke($obj);

        // assert can obtain non-blocking lock when file is unlocked

        $lockedForReading = flock($h, LOCK_SH|LOCK_NB);
        $lockedForWriting = flock($h, LOCK_EX|LOCK_NB);

        $this->assertTrue($lockedForReading);
        $this->assertTrue($lockedForWriting);

        unset($obj);
        @flock($h, LOCK_UN);
        @fclose($h);
        @unlink($fileName);
    }
}