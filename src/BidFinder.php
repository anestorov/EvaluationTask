<?php
namespace AdCash;

use AdCash\lib\CsvParser;
use AdCash\models\BidFinderSolution;
use AdCash\models\BidRecord;

class BidFinder {
    private ?BidRecord $maxBidRecord = null;
    private ?BidRecord $secondMaxBidRecord = null;
    
    function __construct(private string $fileURL) {
    }

    function find(): BidFinderSolution {
        
        $parser = new CsvParser(BidRecord::class);
        $parser->parse($this->fileURL, function($record){
        
            if ($this->maxBidRecord === null) {
                $this->maxBidRecord = $record;
            } 
        
            if($this->secondMaxBidRecord === null) {
                $this->secondMaxBidRecord = $record;
            }
        
            if ($record->bid > $this->maxBidRecord->bid) {
                $this->secondMaxBidRecord = $this->maxBidRecord;
                $this->maxBidRecord = $record;
            } else if ($record->bid > $this->secondMaxBidRecord->bid) {
                $this->secondMaxBidRecord = $record;
            }
        });
        
        return new BidFinderSolution($this->maxBidRecord->id, $this->secondMaxBidRecord->bid);
    }
        
}