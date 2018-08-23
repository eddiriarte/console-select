<?php
namespace Tests\Traits;

use EddIriarte\Console\Helpers\SelectionHelper;
use EddIriarte\Console\Traits\SelectableInputs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelectableInputsTest extends TestCase
{
    /**
     * @test
     */
    public function it_enables_select_helper_set()
    {
        $trait = $this->getMockForTrait(
            SelectableInputs::class,
            [],
            '',
            true,
            true,
            true,
            ['getInput', 'getOutput', 'getHelperSet']
        );

        $helperSet = new HelperSet();
        $trait->method('getHelperSet')
            ->will($this->returnValue($helperSet));

        $inputMock = $this->getMockBuilder(InputInterface::class)->getMock();
        $outputMock = $this->getMockBuilder(OutputInterface::class)->getMock();
        $outputMock->method('getFormatter')
            ->will($this->returnValue(new OutputFormatter()));

        $trait->enableSelectHelper($inputMock, $outputMock);

        $this->assertTrue($helperSet->has('selection'), 'Selection helper was not enabled');
    }

    /**
     * @test
     */
    public function it_does_select_single()
    {
        $trait = $this->getMockForTrait(
            SelectableInputs::class,
            [],
            '',
            true,
            true,
            true,
            ['getHelper']
        );

        $helperMock = $this->getMockBuilder(SelectionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->method('select')
            ->will($this->returnValue([]));

        $trait->method('getHelper')
            ->will($this->returnValueMap([
                ['selection', $helperMock],
            ]));

        $selection = $trait->select(
            'Select an item from the list',
            ['a', 'b', 'c'],
            false
        );

        $this->assertEquals([], $selection);
    }

    /**
     * @test
     */
    public function it_does_select_multiple()
    {
        $trait = $this->getMockForTrait(
            SelectableInputs::class,
            [],
            '',
            true,
            true,
            true,
            ['getHelper']
        );

        $helperMock = $this->getMockBuilder(SelectionHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->method('select')
            ->will($this->returnValue([]));

        $trait->method('getHelper')
            ->will($this->returnValueMap([
                ['selection', $helperMock],
            ]));

        $selection = $trait->select(
            'Select an item from the list',
            ['a', 'b', 'c']
        );

        $this->assertEquals([], $selection);
    }
}
