<?php
namespace Tests\Inputs;

use EddIriarte\Console\Inputs\AbstractSelect;
use PHPUnit\Framework\TestCase;

class AbstractSelectTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_message()
    {
        $select = $this->getMockForAbstractClass(
            AbstractSelect::class,
            [
                'message' => 'Select an item from the list:',
                'options' => ['a', 'b', 'c'],
            ]
        );

        $message = $select->getMessage();

        $this->assertEquals('Select an item from the list:', $message);
    }

    /**
     * @test
     */
    public function it_gets_options()
    {
        $select = $this->getMockForAbstractClass(
            AbstractSelect::class,
            [
                'message' => 'Select an item from the list:',
                'options' => ['a', 'b', 'c'],
            ]
        );

        $options = $select->getOptions();

        $this->assertCount(3, $options);
    }

    /**
     * @test
     */
    public function it_gets_selections()
    {
        $select = $this->getMockForAbstractClass(
            AbstractSelect::class,
            [
                'message' => 'Select an item from the list:',
                'options' => ['a', 'b', 'c'],
            ]
        );

        // $select->method('select')
        //     ->will($this->returnCallback(function ($option) {
        //         $this->selections[] = $option;
        //     }));

        $selections = $select->getSelections();

        $this->assertCount(0, $selections);

        // $select->select('b');
        // $selections = $select->getSelections();
        // $this->assertCount(1, $selections);
    }

    /**
     * @test
     */
    public function it_has_selections()
    {
        $select = $this->getMockForAbstractClass(
            AbstractSelect::class,
            [
                'message' => 'Select an item from the list:',
                'options' => ['a', 'b', 'c'],
            ]
        );

        $hasSelections = $select->hasSelections();
        $this->assertFalse($hasSelections);

        // $select->select('c');
        // $hasSelections = $select->hasSelections();
        // $this->assertTrue($hasSelections);
    }

    /**
     * @test
     */
    public function it_is_selected()
    {
        $select = $this->getMockForAbstractClass(
            AbstractSelect::class,
            [
                'message' => 'Select an item from the list:',
                'options' => ['a', 'b', 'c'],
            ]
        );

        // $select->select('c');

        $this->assertFalse($select->isSelected('a'));
        $this->assertFalse($select->isSelected('b'));
        // $this->assertTrue($select->isSelected('c'));
    }
}
