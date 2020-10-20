<?php
namespace Tests\Inputs;

use EddIriarte\Console\Providers\SelectServiceProvider;
use Illuminate\Console\Command;
use PHPUnit\Framework\TestCase;
use Tests\InputOutputStreamMocks;
use Tests\Key;

class SelectServiceProviderTest extends TestCase
{
    use InputOutputStreamMocks;

    /**
     * @test
     */
    public function it_registers_command_macro()
    {
        $laravel = $this->createMock(\Illuminate\Contracts\Foundation\Application::class);

        $provider = new SelectServiceProvider($laravel);

        $this->assertFalse(Command::hasMacro('select'));

        $provider->boot();

        $this->assertTrue(Command::hasMacro('select'));
    }

    /**
     * @test
     */
    public function it_calls_select_macro()
    {
        $output = $this->getMockBuilder(\Illuminate\Console\OutputStyle::class)
            ->disableOriginalConstructor()
            ->getMock();
        $output->method('getFormatter')
            ->willReturnCallback(function () {
                $formatter = $this->getMockBuilder(\Symfony\Component\Console\Formatter\OutputFormatterInterface::class)
                    ->getMock();
                $formatter->method('hasStyle')->willReturn(true);

                return $formatter;
            });

        $stream = $this->getInputStream(
            Key::RIGHT .
            Key::SELECT .
            Key::SUBMIT
        );
        
        $input = $this->createStreamableInputInterface($stream);

        $laravel = $this->createMock(\Illuminate\Contracts\Foundation\Application::class);
        $provider = new SelectServiceProvider($laravel);
        $provider->boot();

        $command = new Command($laravel);
        $command->setInput($input);
        $command->setOutput($output);

        $result = $command->select('foo or bar?', ['foo', 'bar']);
        
        $this->assertEquals('bar', $result[0]);
    }
}