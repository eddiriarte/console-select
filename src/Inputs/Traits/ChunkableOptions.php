<?php
namespace EddIriarte\Console\Inputs\Traits;

use EddIriarte\Console\Inputs\Exceptions\IndexOutOfRange;


/**
 * Trait ChunkableOptions
 * @package EddIriarte\Console\Inputs\Traits
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
trait ChunkableOptions
{
    protected $chunks;

    protected $chunkSize = 3;

    /**
     * @return array
     */
    public function getChunks(int $chunkSize = null): array
    {
        if (!is_null($chunkSize)) {
            $this->chunkSize = $chunkSize;
        }

        if (!isset($this->chunks)) {
            $this->chunks = array_chunk($this->getOptions(), $this->chunkSize);
        }

        return $this->chunks;
    }

    /**
     * @param int $index
     * @return array
     * @throws EddIriarte\Console\Inputs\Exceptions\IndexOutOfRange
     */
    public function getChunkAt(int $index): array
    {
        if (!empty($this->getChunks()[$index])) {
            return $this->getChunks()[$index];
        }

        throw new IndexOutOfRange($index);
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
     * @throws EddIriarte\Console\Inputs\Exceptions\IndexOutOfRange
     */
    public function getEntryAt(int $rowIndex, int $colIndex): string
    {
        if ($this->hasEntryAt($rowIndex, $colIndex)) {
            return $this->getChunks()[$rowIndex][$colIndex];
        }

        throw new IndexOutOfRange("{$rowIndex}:{$colIndex}");
    }
}
