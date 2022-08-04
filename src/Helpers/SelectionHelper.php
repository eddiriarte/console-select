<?php
namespace EddIriarte\Console\Helpers;

use EddIriarte\Console\Handlers\SelectHandler;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use EddIriarte\Console\Inputs\Interfaces\SelectInput;

/**
 * Class SelectionHelper
 *
 * Its used for registration to symfonys output helpers.
 *
 * @package EddIriarte\Console\Helpers
 * @author Eduardo Iriarte <eddiriarte[at]gmail[dot]com>
 */
class SelectionHelper implements HelperInterface
{
    use StreamableInput;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var null
     */
    protected $helperSet = null;

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
        $this->setOutputStyles();
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
    public function getHelperSet():HelperSet
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
     * Allow multiple item selections to user.
     *
     * @param SelectInput $question
     * @return array
     */
    public function select(SelectInput $question)
    {
        $select = new SelectHandler($question, $this->output, $this->getInputStream());

        $responses = $select->handle();
        // TODO: validate responses  ???

        return $responses;
    }

    /**
     *
     */
    protected function checkAnsiSupport(): void
    {
        if ($this->output->isDecorated()) {
            return;
        }

        // // disable overwrite when output does not support ANSI codes.
        // $this->overwrite = false;
        // // set a reasonable redraw frequency so output isn't flooded
        // $this->setRedrawFrequency(10);
    }

    protected function setOutputStyles()
    {
        if (!$this->output->getFormatter()->hasStyle('hl')) {
            $style = new OutputFormatterStyle('black', 'white');
            $this->output->getFormatter()->setStyle('hl', $style);
        }
    }
}
