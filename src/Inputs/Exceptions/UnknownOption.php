<?php
namespace EddIriarte\Console\Inputs\Exceptions;

class UnknownOption extends \Exception
{
    public function __construct(string $option)
    {
        parent::__construct("Option \"$option\" does not exists.");
    }
}
