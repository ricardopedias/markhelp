<?php
declare(strict_types=1);

namespace Tests\Bags;

use MarkHelp\Bags\Asset;
use PHPUnit\Framework\TestCase;

class AssetTest extends TestCase
{
    /**
     * @test
     */
    public function defaults()
    {
        $bag = new Asset;
        $this->assertEquals([
            'mountPoint'    => 'origin',
            'assetParam'    => 'assets.none',
            'assetPath'     => 'assets/none.css',
            'assetBasename' => 'none.css',
        ], $bag->all());
    }
}