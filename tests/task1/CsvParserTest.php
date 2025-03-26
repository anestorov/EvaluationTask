<?php

use PHPUnit\Framework\TestCase;
use Task1\lib\CsvParser;
use Task1\models\BidRecord;

class CsvParserTest extends TestCase
{
    private $fileName = 'data/task1/test.csv';
    function tearDown(): void
    {
        @unlink($this->fileName);
    }
    public function test_parset_valid_csv()
    {

        file_put_contents($this->fileName, "id,bid\n1,1.0\n2,2.0\n3,3.0\n");

        $records = [];
        $csvParser = new CsvParser(BidRecord::class);
        $csvParser->parse($this->fileName, function ($record) use (&$records) {
            $records[] = $record;
        });

        $this->assertCount(3, $records);

        $this->assertEquals(1, $records[0]->id);
        $this->assertEquals(1.0, $records[0]->bid);

        $this->assertEquals(2, $records[1]->id);
        $this->assertEquals(2.0, $records[1]->bid);

        $this->assertEquals(3, $records[2]->id);
        $this->assertEquals(3.0, $records[2]->bid);
    }

    public function test_parse_inconsistent_shape_csv()
    {
        $this->fileName = 'data/task1/test.csv';

        file_put_contents($this->fileName, "id,bid,time\n1,1.0,555\n2,2.0\n3,3.0\n");

        $records = [];
        $csvParser = new CsvParser(BidRecord::class);
        $csvParser->parse($this->fileName, function ($record) use (&$records) {
            $records[] = $record;
        });

        $this->assertCount(3, $records);

        $this->assertEquals(1, $records[0]->id);
        $this->assertEquals(1.0, $records[0]->bid);
    }

    public function test_parse_invalid_value_in_csv()
    {
        file_put_contents($this->fileName, "id,bid\n1,1.0\nid1,2.0\n3,nan\n");

        $this->expectException(\Exception::class);

        $records = [];
        $csvParser = new CsvParser(BidRecord::class);
        $csvParser->parse($this->fileName, function ($record) use (&$records) {
            $records[] = $record;
        });
    }
}
