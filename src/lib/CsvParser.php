<?php
namespace AdCash\lib;

use AdCash\interfaces\Model;

/**
 * Class CsvParser
 * @package AdCash\lib
 * @template T of Model
 */
class CsvParser
{
    /**
     * CsvParser constructor.
     * @param class-string<T> $rowClass
     * @param bool $skipLabels
     * @throws \Exception
     */
    public function __construct(private string $modelClass, private bool $skipLabels = true)
    {
        if (!is_subclass_of($modelClass, Model::class)) {
            throw new \Exception("Class must implement MyInterface");
        }
    }

    /**
     * @param string $filename
     * @param callable(T): void $callback
     */
    public function parse(string $filename, callable $callback)
    {
        $h = fopen($filename, "r");

        if (!$h) {
            die("Cannot open file");
        }

        $labelsSkipped = false;

        while ($line = fgetcsv($h, escape: '"')) {
            if ($this->skipLabels && !$labelsSkipped) {
                $labelsSkipped = true;
                continue;
            }
            $model = new $this->modelClass();
            $model->setFromArray($line);
            $callback($model);
        }
        fclose($h);
    }
}
