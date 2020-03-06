<?php
declare(strict_types=1);

namespace Tests\Bags;

use MarkHelp\Bags\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @test
     */
    public function defaults()
    {
        $bag = new File;
        $this->assertEquals([
            'type'         => 'page',
            'assetsPrefix' => './',
            'pathSearch'   => 'none.md',
            'pathReplace'  => 'none.html',
        ], $bag->all());
    }
}