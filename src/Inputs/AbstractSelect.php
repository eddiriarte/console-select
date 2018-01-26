<?php
namespace EddIriarte\Console\Inputs;

use EddIriarte\Console\Interfaces\SelectInput;
use EddIriarte\Console\Traits\OptionChunks;

/**
 * Class AbstractSelect
 * @package EddIriarte\Console\Inputs
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
abstract class AbstractSelect implements SelectInput
{
    use OptionChunks;

    protected $message;

    protected $options;

    protected $selections;

    /**
     * AbstractSelect constructor.
     *
     * @param string $message
     * @param array $options
     */
    public function __construct(string $message, array $options)
    {
        $this->message = $message;
        $this->options = $options;
        $this->selections = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getSelections(): array
    {
        return $this->selections;
    }

    /**
     * @return bool
     */
    public function hasSelections(): bool
    {
        return !empty($this->selections);
    }

    /**
     * @param string $option
     * @return bool
     */
    public function isSelected(string $option): bool
    {
        return (bool) in_array($option, $this->selections);
    }
}
