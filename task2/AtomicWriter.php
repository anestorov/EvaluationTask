<?php

namespace Task2;

class AtomicWriter
{
    private $file = __DIR__ . "/../data/task2/storage.json";

    /** @var String[] */
    private $data = [];

    /** @var resource|false */
    private $fileHandle = false;

    function __construct(string $words, ?string $fileURI = null)
    {
        if ($fileURI !== null) {
            $this->file = $fileURI;
        }

        $this->getFileHandle();

        $this->setExclusiveLock();

        $this->loadFromFile();

        $this->processWords($words);

        $this->saveToFile();

        $this->releaseLock();

        fclose($this->fileHandle);
        $this->fileHandle = false;
    }

    function __destruct()
    {
        if ($this->fileHandle !== false) {
            $this->releaseLock();
            fclose($this->fileHandle);
        }
    }

    private function getFileHandle()
    {
        $this->fileHandle = @fopen($this->file, "c+");
        if ($this->fileHandle === false) throw new \Exception("Could not open file");
    }

    private function addWord($word)
    {
        if (!isset($this->data[$word])) {
            $this->data[$word] = 0;
        }
        $this->data[$word]++;
    }

    private function processWords($words)
    {
        $words = trim($words);
        $words = explode(" ", $words);
        foreach ($words as $word) {
            $word = trim($word);
            $word = strtolower($word);
            $word = preg_replace("/[^a-zA-Z0-9]+/", "", $word);

            $this->addWord($word);
        }
    }

    private function setExclusiveLock()
    {
        if ($this->fileHandle === false) {
            throw new \Exception("File handle is not open");
        }
        if (!flock($this->fileHandle, LOCK_EX)) {
            throw new \Exception("Could not lock file");
        }
    }

    private function releaseLock()
    {
        flock($this->fileHandle, LOCK_UN);
    }

    private function loadFromFile()
    {
        @rewind($this->fileHandle);

        $jsonString = "";
        while (!feof($this->fileHandle)) {
            $read = @fread($this->fileHandle, 4096);
            if ($read === false) {
                throw new \Exception("Could not read file");
            }
            $jsonString .= $read;
        }

        if ($jsonString === "") return;

        $data = json_decode($jsonString, true);

        if ($data !== null && is_array($data)) {
            $this->data = $data;
        } else {
            throw new \Exception("Could not decode JSON");
        }
    }

    private function saveToFile(): void
    {
        $jsonString = json_encode($this->data);

        if ($jsonString === false) {
            throw new \Exception("Could not encode data to JSON");
        }

        if (!@ftruncate($this->fileHandle, 0)) {
            throw new \Exception("Could not clear file");
        }

        if (!@rewind($this->fileHandle)) {
            throw new \Exception("Could not rewind file");
        }

        if (@fwrite($this->fileHandle, $jsonString) === false) {
            throw new \Exception("Could not write to file");
        }

        if (!@fflush($this->fileHandle)) {
            throw new \Exception("Could not flush file");
        }
    }
}
