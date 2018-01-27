<?php
namespace EddIriarte\Console\Traits;

/**
 * Trait OptionChunks
 * @package EddIriarte\Console\Traits
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
trait OptionChunks
{
    protected $chunks;

    /**
     * @return array
     */
    public function getChunks(): array
    {
        if (!isset($this->chunks)) {
            $this->chunks = array_chunk($this->options, 3);
        }

        return $this->chunks;
    }

    /**
     * @param int $index
     * @return array
     * @throws \Exception
     */
    public function getChunkAt(int $index): array
    {
        if (!empty($this->getChunks()[$index])) {
            return $this->getChunks()[$index];
        }

        //TODO implement a proper exception
        throw new \Exception('Unknown index!');
    }

    /**
     * @return int
     */
    public function getChunksCount(): int
    {
        return count($this->getChunks());
    }

    /**
     * @param int $rowIndex
     * @param int $colIndex
     * @return bool
     */
    public function hasEntryAt(int $rowIndex, int $colIndex): bool
    {
        $chunks = $this->getChunks();
        return array_key_exists($rowIndex, $chunks) && array_key_exists($colIndex, $chunks[$rowIndex]);
    }

    /**
     * @param int $rowIndex
     * @param int $colIndex
     * @return string
     * @throws \Exception
     */
    public function getEntryAt(int $rowIndex, int $colIndex): string
    {
        if ($this->hasEntryAt($rowIndex, $colIndex)) {
            return $this->getChunks()[$rowIndex][$colIndex];
        }

        //TODO implement a proper exception
        throw new \Exception('Unknown index!');
    }
}
