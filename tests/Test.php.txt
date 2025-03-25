<?php 
namespace Tests;

class Test {

    function assertTrue(bool $condition): void {
        $trace = debug_backtrace(); 
        $location = $trace[1]['function'] . ' in '. $trace[1]['class'];

        if ($condition) {
            echo "✅ Passed - $location\n";
        } else {
            
            echo "❌ Failed - $location\n";
        }
    }
}