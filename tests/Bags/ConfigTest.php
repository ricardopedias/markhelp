<?php
declare(strict_types=1);

namespace Tests\Bags;

use MarkHelp\Bags\Config;
use ReflectionClass;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    private $pathRoot = 'my/path/root';

    /**
     * @test
     */
    public function defaults()
    {
        $bag = new Config($this->pathRoot);

        $this->assertEquals($this->pathRoot, $bag->param('path.root'));

        // tema padrão
        $reflect = new ReflectionClass(Config::class);
        $parentDir = $this->dirname($this->dirname($reflect->getFilename()));
        $this->assertEquals("{$parentDir}/Themes/default", $bag->param('path.theme'));

        $this->assertFalse($bag->param('generate.phpindex'));

        $this->assertEquals("{{theme}}/assets/logo.png", $bag->param('assets.logo.src'));
        $this->assertTrue($bag->param('logo.status'));

        $this->assertArrayHasKey('project.name', $bag->all());
        $this->assertArrayHasKey('project.slogan', $bag->all());
        $this->assertArrayHasKey('current.page', $bag->all());
        
        $this->assertEquals('https://github.com/ricardopedias/markhelp', $bag->param('github.url'));
        $this->assertTrue($bag->param('github.fork'));
            
        $this->assertArrayHasKey('copy.name', $bag->all());
        $this->assertArrayHasKey('copy.url', $bag->all());
        $this->assertArrayHasKey('created.by', $bag->all());
        $this->assertArrayHasKey('creator.name', $bag->all());
        $this->assertArrayHasKey('creator.url', $bag->all());

        $this->assertEquals("{{theme}}/assets/favicon.ico", $bag->param('assets.icon.favicon'));
        $this->assertEquals("{{theme}}/assets/apple-touch-icon-precomposed.png", $bag->param('assets.icon.apple'));

        $bag->setParam('assets.logo.src', 'changed');
        $this->assertEquals("changed", $bag->param('assets.logo.src'));
    }
}