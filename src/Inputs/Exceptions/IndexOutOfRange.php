<?php
namespace EddIriarte\Console\Inputs\Exceptions;

class IndexOutOfRange extends \Exception
{
    public function __construct(string $index)
    {
        parent::__construct("Index \"$index\" does not exists.");
    }
}