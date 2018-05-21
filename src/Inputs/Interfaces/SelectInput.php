<?php
namespace EddIriarte\Console\Inputs\Interfaces;

/**
 * Interface SelectInput
 * @package EddIriarte\Console\Inputs\Interfaces
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
interface SelectInput
{
    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return array
     */
    public function getSelections(): array;

    /**
     * @return bool
     */
    public function hasSelections(): bool;

    /**
     * @param string $option
     * @return bool
     */
    public function isSelected(string $option): bool;

    /**
     * @param string $option
     */
    public function select(string $option): void;
}
