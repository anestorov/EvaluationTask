<?php 
namespace Tests;

class Test {

    function assertTrue(bool $condition): void {
        $trace = debug_backtrace(); 
        $location = $trace[1]['function'] . ' in '. $trace[1]['class'];

        if ($condition) {
            echo "Test passed - $location\n";
        } else {
            
            echo "Test failed - $location\n";
        }
    }
}