<?php

namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Inputs\Exceptions\UnknownOption;


/**
 * Class RadioInput
 * @package EddIriarte\Console\Inputs
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
class RadioInput extends AbstractSelect
{
    /**
     * {@inheritdoc}
     */
    public function select(string $option): void
    {
        if (empty(array_intersect($this->options, [$option]))) {
            throw new UnknownOption($option);
        }

        $this->selections = $this->isSelected($option) ? [] : [$option];
    }
}
