<?php
namespace EddIriarte\Console\Helpers;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

/**
 * Class SelectionHelper
 * @package EddIriarte\Console\Helpers
 * @author Eduardo Iriarte <eddiriarte@gmail.com>
 */
class SelectionHelper implements HelperInterface
{
    protected $input;
    protected $output;
    protected $question;
    protected $terminal;
    protected $overwrite;
    protected $helperSet = null;
    protected $optionsLineCount;
    protected $firstRun;
    protected $inputStream;

    /**
     * SelectionHelper constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->checkAnsiSupport();
    }

    /**
     * Allow multiple item selections to user.
     *
     * @param SelectInput $question
     * @return array
     */
    public function select(SelectInput $question)
    {
        $this->question = $question;
        $this->firstRun = true;
        $this->inputStream = null;
        $this->terminal = new Terminal();

        $this->inputStream = STDIN;
        if ($this->input instanceof StreamableInputInterface && $stream = $this->input->getStream()) {
            $this->inputStream = $stream ?: STDIN;
        }

        $responses = [];
        $i = 0;
        do {
            $this->navigate($this->output, $this->question, $this->inputStream, $this->question->getOptions());
            $i++;
        } while ($i < 10);

        var_dump($responses);
        return $responses;
    }

    protected function checkAnsiSupport(): void
    {
        if ($this->output->isDecorated()) {
            return;
        }

        // disable overwrite when output does not support ANSI codes.
        $this->overwrite = false;
        // set a reasonable redraw frequency so output isn't flooded
        $this->setRedrawFrequency(10);
    }

    /**
     * @param SelectInput $question
     * @param int $col
     * @param int $row
     */
    public function overwrite(SelectInput $question, int $col, int $row)
    {
        $message = $this->getOptionsMessage($question, $col, $row);
        if (!$this->firstRun) {
            // Move the cursor to the beginning of the line
            $this->output->write("\x0D");
            // Erase the line
            $this->output->write("\x1B[2K");
            // Erase previous lines
            $lines = $question->getChunksCount() - 1;
            if ($lines > 0) {
                $this->output->write(str_repeat("\x1B[1A\x1B[2K", $lines));
            }
        }

        $this->firstRun = false;
        $this->output->write($message);
    }

    /**
     * @param SelectInput $question
     * @param int $col
     * @param int $row
     * @return string
     */
    public function getOptionsMessage(SelectInput $question, int $col, int $row): string
    {
        $chunks = $question->getChunks();
        return join(PHP_EOL, array_map(function ($entries) use ($question, $chunks, $col, $row) {
            $hasCursor = (bool) ($row === array_search($entries, $chunks));
            return $this->formattedRow($entries, $question, $hasCursor ? $col : -10);
        }, $chunks));
    }

    /**
     * @param array $entries
     * @param SelectInput $question
     * @param int $activeCol
     * @return mixed
     */
    protected function formattedRow(array $entries, SelectInput $question, int $activeCol)
    {
        return array_reduce($entries, function ($carry, $item) use ($question, $entries, $activeCol) {
            $isActive = ($activeCol === array_search($item, $entries));
            return $carry . $this->formattedOption($item, $question->isSelected($item), $isActive, 17);
        }, '');
    }

    /**
     * @param string $option
     * @param bool $checked
     * @param bool $active
     * @param int $gapWidth
     * @return string
     */
    protected function formattedOption(string $option, bool $checked, bool $active = false, int $gapWidth = 25): string
    {
        $item = substr($option, 0, ($gapWidth - 1));
        return sprintf(
            ' [<info>%1$s</info>] <%4$s>%2$s</%4$s>%3$s',
            ($checked ? '✔' : ' '),
            $item,
            str_repeat(' ', $gapWidth - strlen($item)),
            ($active ? 'info' : 'comment')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setHelperSet(HelperSet $helperSet = null)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'selection';
    }

    /**
     * @param SelectInput $question
     * @param int $col
     * @param int $row
     * @param bool $hasCursor
     */
    private function overwriteLine(SelectInput $question, int $col, int $row, bool $hasCursor = false)
    {
        $chunk = $question->getChunkAt($row);
        $activeCol = $hasCursor ? $col : -10;
        $message = join('', array_map(function ($item) use ($question, $chunk, $activeCol) {
            $isActive = $activeCol === array_search($item, $chunk);
            return $this->formattedOption($item, $question->isSelected($item), $isActive, 17);
        }, $chunk)) . PHP_EOL;

        // Move the cursor to the beginning of the line
        $this->output->write("\x0D");
        // Erase the line
        $this->output->write("\x1B[2K");
        // Sets new updated line!
        $this->output->write($message);
    }

    /**
     * Navigates through option items.
     *
     * @param OutputInterface $output
     * @param SelectInput $question
     * @param $inputStream
     * @param array $autocomplete
     * @return array
     */
    private function navigate(OutputInterface $output, SelectInput $question, $inputStream, array $options): array
    {
        $selections = [];
        $sttyMode = shell_exec('stty -g');

        $rowOffset = 0;
        $colOffset = 0;

        $this->overwrite($question, $rowOffset, $colOffset);

        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        shell_exec('stty -icanon -echo');

        // Read a keypress
        while (!feof($inputStream)) {
            $char = fread($inputStream, 1);
            if ("\033" === $char) {
                // Did we read an escape sequence?
                $char .= fread($inputStream, 2);
                if (empty($char[2]) || !in_array($char[2], ['A', 'B', 'C', 'D'])) {
                    // Input stream was not an arrow key.
                    continue;
                }

                switch ($char[2]) {
                    case 'A':
                        // go up!
                        if ($rowOffset > 0) {
                            $rowOffset -= 1;
                        }
                        break;
                    case 'B':
                        // go down!
                        if ($rowOffset < ($question->getChunksCount() - 1)) {
                            $rowOffset += 1;
                        }
                        break;
                    case 'C':
                        // go right!
                        if ($colOffset < 2) {
                            $colOffset += 1;
                        }
                        break;
                    case 'D':
                        // go left!
                        if ($colOffset > 0) {
                            $colOffset -= 1;
                        }
                        break;
                }

                $this->overwrite($question, $colOffset, $rowOffset);
            }
            // TODO: mark as selected
            // TODO: exit code!
        }

        // Reset stty so it behaves normally again
        shell_exec(sprintf('stty %s', $sttyMode));

        return $selections;
    }
}
