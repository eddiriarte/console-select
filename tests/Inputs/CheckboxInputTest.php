<?php
namespace Tests\Inputs;

use EddIriarte\Console\Inputs\CheckboxInput;
use PHPUnit\Framework\TestCase;

class CheckboxInputTest extends TestCase
{
    /**
     * @test
     */
    public function it_selects()
    {
        $checkbox = new CheckboxInput('Select an item!', ['a', 'b', 'c']);

        $checkbox->select('a');
        $selections = $checkbox->getSelections();
        $this->assertCount(1, $selections);
        $this->assertEquals('a', $selections[0]);

        $checkbox->select('b');
        $selections = $checkbox->getSelections();
        $this->assertCount(2, $selections);
        $this->assertEquals('b', $selections[1]);

        $checkbox->select('c');
        $selections = $checkbox->getSelections();
        $this->assertCount(3, $selections);
        $this->assertEquals('c', $selections[2]);
    }

    /**
     * @test
     * @expectedException  EddIriarte\Console\Inputs\Exceptions\UnknownOption
     */
    public function it_throws_exception_on_selects()
    {
        $checkbox = new CheckboxInput('Select an item!', ['a', 'b', 'c']);

        $checkbox->select('f');
    }
}
