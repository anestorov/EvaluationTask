<?php

namespace Task1;

use Task1\lib\CsvParser;
use Task1\models\BidFinderSolution;
use Task1\models\BidRecord;

/**
 * This class is responsible for finding the highest and second highest bid from a CSV file
 * 
 * @package AdCash
 */
class BidFinder
{
    private ?BidRecord $maxBidRecord = null;
    private ?BidRecord $secondMaxBidRecord = null;
    private string $fileURI;

    function __construct(string $fileName)
    {
        $fileURI = __DIR__.'/../data/task1/' . $fileName;
        if (!file_exists($fileURI)) {
            throw new \Exception("File URI supplied to BidFinder was not found");
        }
        $this->fileURI = $fileURI;
    }

    /**
     * This function finds the highest and second highest bid from a CSV file
     * 
     * @return BidFinderSolution
     */
    function find(): BidFinderSolution
    {
        $parser = new CsvParser(modelClass: BidRecord::class);
        $parser->parse($this->fileURI, function ($currentRecord) {

            // Initialize the max and second max bid records
            if ($this->maxBidRecord === null) {
                $this->maxBidRecord = $currentRecord;
            }

            // Initialize the second max bid record
            if ($this->secondMaxBidRecord === null) {
                $this->secondMaxBidRecord = $currentRecord;
            }

            // Update the max and second max bid records
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
