<?php

namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Interfaces\SelectInput;
use EddIriarte\Console\Traits\OptionChunks;

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
        $this->selections = $this->isSelected($option) ? [] : [$option];
    }
}
