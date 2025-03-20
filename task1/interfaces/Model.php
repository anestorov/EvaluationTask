<?php

namespace Task1\interfaces;

/**
 * Interface of data models
 * 
 * @package AdCash
 */
interface Model
{
    function setFromArray(array $data): void;
}
