<?php

namespace Task2;

class AtomicReader
{
    private $file = __DIR__ . "/../data/task2/storage.json";

    /** @var String[] */
    private $data = [];

    /** @var resource|false */
    private $fileHandle = false;

    function __construct(?string $fileURI = null)
    {
        if ($fileURI !== null) {
            $this->file = $fileURI;
        }

        $this->load();
    }

    function __destruct()
    {
        if ($this->fileHandle !== false) {
            $this->releaseLock();
            fclose($this->fileHandle);
        }
    }

    public function load()
    {
        $this->getFileHandle();
        $this->setSharedLock();
        $this->loadFromFile();
        $this->releaseLock();
        fclose($this->fileHandle);
        $this->fileHandle = false;
    }

    public function getWordCount($word)
    {
        return $this->data[$word] ?? 0;
    }

    public function getWords()
    {
        return $this->data;
    }

    private function getFileHandle()
    {
        if (file_exists($this->file) === false) {
            file_put_contents($this->file, '{}');
        }
        $this->fileHandle = @fopen($this->file, "r");
        if ($this->fileHandle === false) throw new \Exception("Could not open file");
    }

    private function loadFromFile()
    {
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

    private function setSharedLock()
    {
        if (!flock($this->fileHandle, LOCK_SH)) {
            throw new \Exception("Could not lock file");
        }
    }

    private function releaseLock()
    {
        if (!flock($this->fileHandle, LOCK_UN)) {
            throw new \Exception("Could not unlock file");
        }
    }
}
