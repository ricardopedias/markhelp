<?php
declare(strict_types=1);

namespace Tests\Bags;

use MarkHelp\Bags\Bag;
use PHPUnit\Framework\TestCase;

class BagTest extends TestCase
{
    /**
     * @test
     */
    public function setParam()
    {
        $bag = new Bag;
        $bag->setParam('ricardo', 'pereira');
        $this->assertEquals('pereira', $bag->param('ricardo'));
        
        $bag->setParam('developer', true);
        $this->assertTrue($bag->param('developer'));

        $bag->setParam('clean-coder', ['test-array']);
        $this->assertIsArray($bag->param('clean-coder'));

        $bag->setParam('clean-coder', 'changed');
        $this->assertEquals('changed', $bag->param('clean-coder'));

        $this->assertEquals([
            'ricardo'     => 'pereira',
            'developer'   => true,
            'clean-coder' => 'changed',
        ], $bag->all());
    }

    /**
     * @test
     */
    public function addParams()
    {
        $bag = new Bag;
        $bag->setParam('ricardo', 'one');
        $bag->setParam('developer', 'two');
        $bag->setParam('clean-coder', 'three');

        $bag->addParams([
            'pragmatic'   => 'yes',
            'clean-coder' => 'changed'
        ]);

        $this->assertEquals([
            'ricardo'     => 'one',
            'developer'   => 'two',
            'clean-coder' => 'changed',
            'pragmatic'   => 'yes'
        ], $bag->all());
    }

    /**
     * @test
     */
    public function getParamNotExists()
    {
        $bag = new Bag;
        $bag->setParam('ricardo', 'one');
        $this->assertNotNull($bag->param('ricardo'));
        $this->assertNull($bag->param('pereira'));
    }
}