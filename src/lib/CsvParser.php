<?php

namespace AdCash\lib;

use AdCash\interfaces\Model;

/**
 * Class CsvParser is a generic CSV parser that can parse CSV files 
 * and calls a callback function when processing each row
 * 
 * @package AdCash\lib
 * @template T of Model
 */
class CsvParser
{
    /**
     * CsvParser constructor. It requires the class of the data model for each row
     * 
     * @param class-string<T> $rowClass The class of the row model
     * @param bool $skipHeader Whether to skip the header row
     * @param string $separator The separator character
     * @param string $enclosure The enclosure character
     * @param string $escape The escape character
     * @throws \Exception
     */
    public function __construct(
        private string $modelClass,
        private bool $skipHeader = true,
        private string $separator = ',',
        private string $enclosure = '"',
        private string $escape = '\\'
    ) {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \Exception("Supplied Model Class must implement Model Interface");
        }
    }

    /**
     * Opens file pointer and process it line by line. 
     * For each line it calls the callback function with the parsed model
     * 
     * @param string $filename
     * @param callable(T): void $callback
     */
    public function parse(string $fileURI, callable $callback)
    {
        $h = fopen($fileURI, "r");

        if (!$h) {
            throw new \Exception("Could not open file $fileURI");
        }

        $headerSkipped = false;

        while ($line = $this->parseRow($h)) {
            if ($this->skipHeader && !$headerSkipped) {
                $headerSkipped = true;
                continue;
            }
            $model = new $this->modelClass();
            $model->setFromArray($line);
            $callback($model);
        }
        fclose($h);
    }

    /**
     * Abstracts fgetcsv() function
     * 
     * @param resource $fileHandle
     * @return array|false
     */
    private function parseRow($fileHandle): array|false
    {
        return fgetcsv(
            stream: $fileHandle,
            separator: $this->separator,
            enclosure: $this->enclosure,
            escape: $this->escape
        );
    }
}
