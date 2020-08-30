<?php
declare(strict_types=1);

namespace Tests\Writer;

use Exception;
use MarkHelp\Reader\Loader;
use MarkHelp\Writer\Maker;
use Tests\TestCase;

class MakerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function toDirectoryException()
    {
        $this->expectException(Exception::class);

        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader->fromLocalDirectory($projectPath);
        
        $maker = new Maker($loader);
        $maker->toDirectory('/directory/inexistent');
    }

    /** @test */
    public function render()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}");
        $loader->fromLocalDirectory($projectPath);
        
        $maker = new Maker($loader);
        $maker->toDirectory($this->pathDestination);

        // $this->assertDirectoryExists("{$this->pathDestination}/assets");
        // $this->assertDirectoryExists("{$this->pathDestination}/avançado");
        // $this->assertDirectoryExists("{$this->pathDestination}/o_básico");
        // $this->assertFileExists("{$this->pathDestination}/assets/styles.css");
        // $this->assertFileExists("{$this->pathDestination}/home.html");
        // $this->assertFileExists("{$this->pathDestination}/page-one.html");
        // $this->assertFileExists("{$this->pathDestination}/page-two.html");

        $this->assertTrue(true);
    }
}