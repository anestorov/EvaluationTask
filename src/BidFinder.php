<?php

namespace AdCash;

use AdCash\lib\CsvParser;
use AdCash\models\BidFinderSolution;
use AdCash\models\BidRecord;

/**
 * This class is responsible for finding the highest and second highest bid from a CSV file
 * 
 * @package AdCash
 */
class BidFinder
{
    private ?BidRecord $maxBidRecord = null;
    private ?BidRecord $secondMaxBidRecord = null;

    function __construct(private string $fileURI)
    {
        if (!file_exists($fileURI)) {
            throw new \Exception("File URI supplied to BidFinder was not found");
        }
    }

    function find(): BidFinderSolution
    {
        $parser = new CsvParser(modelClass: BidRecord::class);
        $parser->parse($this->fileURI, function ($currentRecord) {

            if ($this->maxBidRecord === null) {
                $this->maxBidRecord = $currentRecord;
            }

            if ($this->secondMaxBidRecord === null) {
                $this->secondMaxBidRecord = $currentRecord;
            }

            if ($currentRecord->bid > $this->maxBidRecord->bid) {

                $this->secondMaxBidRecord = $this->maxBidRecord;
                $this->maxBidRecord = $currentRecord;
            } else if ($currentRecord->bid > $this->secondMaxBidRecord->bid) {

                $this->secondMaxBidRecord = $currentRecord;
            }
        });

        if ($this->maxBidRecord === null || $this->secondMaxBidRecord === null) {
            throw new \Exception("No records processed in the CSV file");
        }

        return new BidFinderSolution($this->maxBidRecord->id, $this->secondMaxBidRecord->bid);
    }
}
