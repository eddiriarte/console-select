<?php
namespace EddIriarte\Console\Handlers;

use EddIriarte\Console\Interfaces\SelectInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SelectHandler
 * @package Lazzier\Helpers
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
class SelectHandler
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var SelectInput
     */
    protected $question;

    /**
     * @var int
     */
    protected $row;

    /**
     * @var int
     */
    protected $column;

    /**
     * @var bool
     */
    protected $firstRun;

    /**
     * SelectStreamHandler constructor.
     * @param SelectInput $question
     * @param OutputInterface $output
     * @param $stream
     */
    public function __construct(SelectInput $question, OutputInterface $output, $stream)
    {
        $this->row = 0;
        $this->column = 0;
        $this->question = $question;
        $this->output = $output;
        $this->stream = $stream;
    }

    /**
     * Navigates through option items.
     *
     * @return array
     */
    public function handle(): array
    {
        $this->firstRun = true;
        $this->output->writeln(
            '<info>' . $this->question->getMessage() . '</info> [<comment>SPACE=select</>, <comment>ENTER=submit</>]'
        );
        $this->repaint();

        $sttyMode = shell_exec('stty -g');

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');

        // Read a keypress
        while (!feof($this->stream)) {
            $char = fread($this->stream, 1);
            if (" " === $char) {
                $this->tryCellSelection();
            } elseif ("\033" === $char) {
                $this->tryCellNavigation($char);
            } elseif (10 === ord($char)) {
                //TODO handle valid state...
                $this->output->write($char);
                break;
            }
            $this->repaint();
        }

        // Reset stty so it behaves normally again
        shell_exec(sprintf('stty %s', $sttyMode));

        $this->output->writeln('> ' . join(', ', $this->question->getSelections()));

        return $this->question->getSelections();
    }

    /**
     * @param $row
     * @param $column
     * @return bool
     */
    protected function exists($row, $column): bool
    {
        return $this->question->hasEntryAt($row, $column);
    }

    /**
     *
     */
    protected function up(): void
    {
        if ($this->exists($this->row - 1, $this->column)) {
            $this->row -= 1;
        }
    }

    /**
     *
     */
    protected function down(): void
    {
        if ($this->exists($this->row + 1, $this->column)) {
            $this->row += 1;
        }
    }

    /**
     *
     */
    protected function left(): void
    {
        if ($this->exists($this->row, $this->column - 1)) {
            $this->column -= 1;
        }
    }

    /**
     *
     */
    protected function right(): void
    {
        if ($this->exists($this->row, $this->column + 1)) {
            $this->column += 1;
        }
    }

    /**
     *
     */
    protected function tryCellSelection(): void
    {
        // Try to select cell.
        if ($this->exists($this->row, $this->column)) {
            $option = $this->question->getEntryAt($this->row, $this->column);
            $this->question->select($option);
        }
    }

    /**
     * @param $char
     */
    protected function tryCellNavigation($char): void
    {
        // Did we read an escape sequence?
        $char .= fread($this->stream, 2);
        if (empty($char[2]) || !in_array($char[2], ['A', 'B', 'C', 'D'])) {
            // Input stream was not an arrow key.
            return;
        }

        switch ($char[2]) {
            case 'A': // go up!
                $this->up();
                break;
            case 'B': // go down!
                $this->down();
                break;
            case 'C': // go right!
                $this->right();
                break;
            case 'D': // go left!
                $this->left();
                break;
        }
    }

    /**
     *
     */
    protected function repaint(): void
    {
        $message = $this->message();
        if (!$this->firstRun) {
            $this->clear();
        }

        $this->firstRun = false;
        $this->output->write($message);
    }

    /**
     *
     */
    protected function clear(): void
    {
        // Move the cursor to the beginning of the line
        $this->output->write("\x0D");
        // Erase the line
        $this->output->write("\x1B[2K");
        // Erase previous lines
        $lines = $this->question->getChunksCount() - 1;
        if ($lines > 0) {
            $this->output->write(str_repeat("\x1B[1A\x1B[2K", $lines));
        }
    }

    /**
     * @return string
     */
    public function message(): string
    {
        $chunks = $this->question->getChunks();
        return join(PHP_EOL, array_map(function ($entries) use ($chunks) {
            $hasCursor = (bool) ($this->row === array_search($entries, $chunks));
            return $this->makeRow($entries, ($hasCursor ? $this->column : -10));
        }, $chunks));
    }

    /**
     * @param array $entries
     * @param int $activeColumn
     * @return mixed
     */
    protected function makeRow(array $entries, int $activeColumn)
    {
        return array_reduce($entries, function ($carry, $item) use ($entries, $activeColumn) {
            $isActive = ($activeColumn === array_search($item, $entries));
            return $carry . $this->makeCell($item, $isActive, 17);
        }, '');
    }

    /**
     * @param string $option
     * @param bool $hasCursor
     * @param int $maxWidth
     * @return string
     */
    protected function makeCell(string $option, bool $hasCursor = false, int $maxWidth = 20): string
    {
        $name = substr($option, 0, ($maxWidth - 1));
        return sprintf(
            ' [<info>%1$s</info>] <%4$s>%2$s</>%3$s',
            ($this->question->isSelected($option) ? '✔' : ' '),
            $name,
            str_repeat(' ', $maxWidth - strlen($name)),
            ($hasCursor ? 'info' : 'comment')
        );
    }
}
