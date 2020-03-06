<?php
declare(strict_types=1);

namespace Tests\Bags;

use MarkHelp\Bags\Support;
use PHPUnit\Framework\TestCase;

class SupportTest extends TestCase
{
    /**
     * @test
     */
    public function defaults()
    {
        $bag = new Support;
        $this->assertEquals([
            'mountPoint'  => 'origin',
            'supportPath' => 'none.css',
        ], $bag->all());
    }
}