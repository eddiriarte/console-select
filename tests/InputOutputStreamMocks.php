<?php
namespace Tests;

use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\StreamOutput;

trait InputOutputStreamMocks
{
    public function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);
        return $stream;
    }

    public function createStreamableInputInterface($stream = null, $interactive = true)
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

    public function createOutputInterface(bool $canWrite = false)
    {
        return new StreamOutput(
            fopen('php://memory', ($canWrite ? 'w+' : 'r+'), false)
        );
    }
}
