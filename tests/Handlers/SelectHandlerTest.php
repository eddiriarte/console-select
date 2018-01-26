<?php
namespace Tests\Handlers;

use EddIriarte\Console\Handlers\SelectHandler;
use EddIriarte\Console\Inputs\CheckboxInput;
use EddIriarte\Console\Inputs\RadioInput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\StreamOutput;

interface Key
{
    const UP = "\033[C";
    const DOWN = "\033[C";
    const RIGHT = "\033[C";
    const LEFT = "\033[D";
    const SUBMIT = "\n";
    const SELECT = " ";
}

class SelectHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_handles_radio_inputs()
    {
        $question = new RadioInput('Select one', ['one', 'two', 'three']);
        $stream = $this->getInputStream(Key::RIGHT . Key::RIGHT . Key::SELECT . Key::LEFT . Key::SELECT . Key::SUBMIT);
        $output = $this->createOutputInterface();

        $handler = new SelectHandler($question, $output, $stream);

        $handler->handle();

        $selections = $question->getSelections();
        $this->assertCount(1, $selections);
        $this->assertEquals('two', $selections[0]);
    }

    /**
     * @test
     */
    public function it_handles_checkboxes_inputs()
    {
        $question = new CheckboxInput('Select one', ['one', 'two', 'three']);
        $stream = $this->getInputStream(Key::RIGHT . Key::RIGHT . Key::SELECT . Key::LEFT . Key::SELECT . Key::SUBMIT);
        $output = $this->createOutputInterface();

        $handler = new SelectHandler($question, $output, $stream);

        $handler->handle();

        $selections = $question->getSelections();
        $this->assertCount(2, $selections);
        $this->assertEquals('three', $selections[0]);
        $this->assertEquals('two', $selections[1]);
    }

    // /**
    //  * @test
    //  * @dataProvider provideExistenceCheckers
    //  */
    // public function it_checks_option_existence($handler, $row, $column, $expected)
    // {
    //     $exists = $handler->exists($row, $column);

    //     $this->assertEquals($expected, $exists);
    // }

    // public function provideExistenceCheckers()
    // {
    //     $question = new CheckboxInput('Select one', [
    //         'one', 'two', 'three',
    //         'four', 'five', 'six',
    //         'seven',
    //     ]);
    //     $stream = $this->getInputStream("");
    //     $output = $this->createOutputInterface();

    //     $handler = new SelectHandler($question, $output, $stream);

    //     return [
    //         [$handler, 1, 1, true],
    //         [$handler, 4, 1, false],
    //         [$handler, 2, 2, true],
    //         [$handler, 1, 4, false],
    //         [$handler, 3, 3, false],
    //     ];
    // }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);
        return $stream;
    }

    protected function createStreamableInputInterface($stream = null, $interactive = true)
    {
        $mock = $this->getMockBuilder(StreamableInputInterface::class)->getMock();
        $mock->expects($this->any())
            ->method('isInteractive')
            ->will($this->returnValue($interactive));
        if ($stream) {
            $mock->expects($this->any())
                ->method('getStream')
                ->willReturn($stream);
        }

        return $mock;
    }

    protected function createOutputInterface()
    {
        return new StreamOutput(fopen('php://memory', 'r+', false));
    }
}
