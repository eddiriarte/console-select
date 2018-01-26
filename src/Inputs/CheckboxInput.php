<?php
namespace EddIriarte\Console\Inputs;

/**
 * Class CheckboxInput
 * @package EddIriarte\Console\Inputs
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
class CheckboxInput extends AbstractSelect
{
    /**
     * {@inheritdoc}
     */
    public function select(string $option): void
    {
        if ($this->isSelected($option)) {
            $this->selections = array_values(array_diff($this->selections, [$option]));
        } else {
            $this->selections[] = $option;
        }
    }
}
