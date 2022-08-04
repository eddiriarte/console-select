<?php
namespace Tests\Handlers;

use EddIriarte\Console\Handlers\SelectHandler;
use EddIriarte\Console\Inputs\CheckboxInput;
use EddIriarte\Console\Inputs\RadioInput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\InputOutputStreamMocks;
use Tests\Key;

class SelectHandlerTest extends TestCase
{
    use InputOutputStreamMocks;

    /**
     * @test
     */
    public function it_handles_radio_inputs()
    {
        $question = new RadioInput('Select one', ['one', 'two', 'three']);
        $output = $this->createOutputInterface();
        $stream = $this->getInputStream(
            Key::DOWN .
            Key::DOWN .
            Key::SELECT .
            Key::UP .
            Key::SELECT .
            Key::SUBMIT
        );
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
        $output = $this->createOutputInterface();
        $stream = $this->getInputStream(
            Key::DOWN .
            Key::DOWN .
            Key::SELECT .
            Key::UP .
            Key::SELECT .
            Key::SUBMIT
        );

        $handler = new SelectHandler($question, $output, $stream);

        $handler->handle();

        $selections = $question->getSelections();
        $this->assertCount(2, $selections);
        $this->assertEquals('three', $selections[0]);
        $this->assertEquals('two', $selections[1]);
    }

    /**
     * @test
     */
    public function it_handles_selection_toggle()
    {
        $question = new CheckboxInput('Select one', ['one', 'two', 'three']);
        $output = $this->createOutputInterface();
        $stream = $this->getInputStream(
            Key::DOWN .
            Key::DOWN .
            Key::SELECT .
            Key::UP .
            Key::SELECT .
            Key::DOWN .
            Key::SELECT .
            Key::SUBMIT
        );

        $handler = new SelectHandler($question, $output, $stream);

        $handler->handle();

        $selections = $question->getSelections();
        $this->assertCount(1, $selections);
        $this->assertEquals('two', $selections[0]);
    }

    /**
     * @test
     * @dataProvider provideExistenceCheckers
     */
    public function it_checks_option_existence($handler, $row, $column, $expected)
    {
        $exists = $handler->exists($row, $column);

        $this->assertEquals($expected, $exists);
    }

    public function provideExistenceCheckers()
    {
        $question = new CheckboxInput('Select one', [
            'one', 'two', 'three',
            'four', 'five', 'six',
            'seven',
        ]);
        $output = $this->createOutputInterface();
        $stream = $this->getInputStream("");

        $handler = new SelectHandler($question, $output, $stream);

        return [
            [$handler, 0, 0, true],
            [$handler, 3, 0, false],
            [$handler, 1, 1, true],
            [$handler, 0, 3, false],
            [$handler, 2, 2, false],
        ];
    }

//    /**
//     * @test
//     */
//    public function it_clears_checkbox_output()
//    {
//        $question = new CheckboxInput('Select an item', [
//            'one', 'two', 'three',
//        ]);
//
//        $buffer = new TestConsoleBuffer;
//
//        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
//        $output->expects($this->any())
//            ->method('write')
//            ->will($this->returnCallback(\Closure::fromCallable([$buffer, 'write'])));
//
//        $output->expects($this->any())
//            ->method('writeln')
//            ->will($this->returnCallback(\Closure::fromCallable([$buffer, 'writeln'])));
//
//        $stream = $this->getInputStream(Key::SUBMIT);
//        $handler = new SelectHandler($question, $output, $stream);
//
//        $handler->repaint();
//
//        $this->assertCount(1, $buffer->getLines());
//        $before = $buffer->getLines()[0];
//        $handler->clear();
//        $this->assertCount(1, $buffer->getLines());
//        $after = $buffer->getLines()[0];
//
//        $this->assertNotEquals($before, $after);
//    }

    /**
     * @test
     */
    public function it_can_navigate_down()
    {
        $question = new CheckboxInput('Select an item', [
            'one', 'two', 'three', 'four', 'five', 'six',
        ]);

        $buffer = new TestConsoleBuffer;

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
        $output->expects($this->any())
            ->method('write')
            ->will($this->returnCallback(\Closure::fromCallable([$buffer, 'write'])));

        $output->expects($this->any())
            ->method('writeln')
            ->will($this->returnCallback(\Closure::fromCallable([$buffer, 'writeln'])));

        $stream = $this->getInputStream(
            Key::DOWN .
            Key::DOWN .
            Key::DOWN .
            Key::SELECT
        );
        $handler = new SelectHandler($question, $output, $stream);
        $handler->handle();

        list($selection) = $question->getSelections();
        $this->assertEquals('four', $selection);
    }

    /**
     * @test
     */
    public function it_can_navigate_up()
    {
        $question = new CheckboxInput('Select an item', [
            'one', 'two', 'three', 'four', 'five', 'six',
        ]);

        $buffer = new TestConsoleBuffer;

        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
        $output->expects($this->any())
            ->method('write')
            ->will($this->returnCallback(\Closure::fromCallable([$buffer, 'write'])));

        $output->expects($this->any())
            ->method('writeln')
            ->will($this->returnCallback(\Closure::fromCallable([$buffer, 'writeln'])));

        $stream = $this->getInputStream(
            Key::DOWN .
            Key::DOWN .
            Key::UP .
            Key::SELECT
        );
        $handler = new SelectHandler($question, $output, $stream);
        $handler->handle();

        list($selection) = $question->getSelections();
        $this->assertEquals('two', $selection);
    }
}

class TestConsoleBuffer
{
    protected $buffer = "";

    public function write($msg, $level)
    {
        $this->buffer .= $msg;
    }

    public function writeln($msg, $level)
    {
        $this->write($msg . PHP_EOL, $level);
    }

    public function getLines()
    {
        return explode(PHP_EOL, $this->buffer);
    }
}
