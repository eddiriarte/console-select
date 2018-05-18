<?php
namespace Tests\Helpers;

use EddIriarte\Console\Helpers\SelectionHelper;
use EddIriarte\Console\Inputs\CheckboxInput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\InputOutputStreamMocks;
use Tests\Key;

class SelectHandlerTest extends TestCase
{
    use InputOutputStreamMocks;

    /**
     * @test
     */
    public function it_initializes_output_styles()
    {
        $hasStyle = false;
        $formatter = $this->getMockBuilder(OutputFormatterInterface::class)
            ->getMock();
        $formatter->method('hasStyle')->will($this->returnValue($hasStyle));
        $formatter->method('setStyle')
            ->willReturnCallback(function ($name, $style) use (&$hasStyle) {
                $hasStyle = $name === 'hl';
            });

        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
        $output->method('isDecorated')
            ->will($this->returnValue(true));
        $output->method('getFormatter')
            ->will($this->returnValue($formatter));

        $helper = new SelectionHelper($input, $output);
        $this->assertTrue($hasStyle, 'Style was not initialized');
    }

    /**
     * @test
     */
    public function it_gets_name()
    {
        $formatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $formatter->method('hasStyle')->will($this->returnValue(true));
        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
        $output->method('isDecorated')->will($this->returnValue(true));
        $output->method('getFormatter')->will($this->returnValue($formatter));

        $helper = new SelectionHelper($input, $output);

        $name = $helper->getName();
        $this->assertEquals('selection', $name);
    }

    /**
     * @test
     */
    public function it_manipulates_helpersets()
    {
        $formatter = $this->getMockBuilder(OutputFormatterInterface::class)->getMock();
        $formatter->method('hasStyle')->will($this->returnValue(true));
        $input = $this->getMockBuilder(InputInterface::class)->getMock();
        $output = $this->getMockBuilder(OutputInterface::class)->getMock();
        $output->method('isDecorated')->will($this->returnValue(true));
        $output->method('getFormatter')->will($this->returnValue($formatter));

        $helper = new SelectionHelper($input, $output);
        $this->assertTrue(empty($helper->getHelperSet()), 'HelperSet already exists!');

        $helper->setHelperSet(new HelperSet());
        $this->assertFalse(empty($helper->getHelperSet()), "HelperSet isn't  set!");

    }

    /**
     * @test
     */
    public function it_triggers_selection()
    {
        $stream = $this->getInputStream(Key::RIGHT . Key::SELECT);
        $input = $this->createStreamableInputInterface($stream);
        $output = $this->createOutputInterface();

        $helper = new SelectionHelper($input, $output);

        $question = new CheckboxInput('Select one', ['one', 'two', 'three']);
        list($response) = $helper->select($question);

        $this->assertEquals('two', $response);
    }
}
