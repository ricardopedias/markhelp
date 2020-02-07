<?php
declare(strict_types=1);

namespace Tests;

use MarkHelp\Reader;
use MarkHelp\Writer;
use PHPUnit\Framework\TestCase;

class RenderTest extends TestCase
{
    /**
     * @test
     */
    public function render()
    {
        $reader = new Reader(__DIR__ . DIRECTORY_SEPARATOR . 'skeleton');
        $reader->setInfo([

        ]);
        $writer = new Writer($reader);
        $writer->saveTo(__DIR__ . DIRECTORY_SEPARATOR . 'destination');

        $this->assertTrue(true);
    }
}