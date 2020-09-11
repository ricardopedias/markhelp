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
        $this->expectExceptionMessage("Directory /directory/inexistent is not exists");

        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader->fromLocalDirectory($projectPath);
        
        $maker = new Maker($loader);
        $maker->toDirectory('/directory/inexistent');
    }

    /** @test */
    public function toDirectoryEqualOriginExceptionNotEndBar()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Source directory cannot be the same as the destination directory");

        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader->fromLocalDirectory($projectPath);
        
        $maker = new Maker($loader);
        $maker->toDirectory($projectPath);
    }

    /** @test */
    public function toDirectoryEqualOriginExceptionWithEndBar()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Source directory cannot be the same as the destination directory");

        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0/");
        $loader->fromLocalDirectory($projectPath);
        
        $maker = new Maker($loader);
        $maker->toDirectory($projectPath);
    }

    /** @test */
    public function renderSingleRelease()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader->fromLocalDirectory($projectPath);
        
        $maker = new Maker($loader);
        $maker->toDirectory($this->pathDestination);

        $this->assertDirectoryExists("{$this->pathDestination}/assets");
        $this->assertFileExists("{$this->pathDestination}/assets/apple-touch-icon-precomposed.png");
        $this->assertFileExists("{$this->pathDestination}/assets/favicon.ico");
        $this->assertFileExists("{$this->pathDestination}/assets/logo.png");
        $this->assertFileExists("{$this->pathDestination}/assets/scripts.js");
        $this->assertFileExists("{$this->pathDestination}/assets/styles.css");

        $this->assertDirectoryExists("{$this->pathDestination}/images");
        $this->assertFileExists("{$this->pathDestination}/images/example.png");
        $this->assertFileExists("{$this->pathDestination}/images/logo.png");
        $this->assertFileExists("{$this->pathDestination}/index.html");
        $this->assertFileExists("{$this->pathDestination}/page.html");
    }

    /** @test */
    public function renderMultiReleases()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}");
        $loader->fromLocalDirectory($projectPath);
        
        $maker = new Maker($loader);
        $maker->toDirectory($this->pathDestination);

        $this->assertDirectoryExists("{$this->pathDestination}/v1.0.0/assets");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/assets/apple-touch-icon-precomposed.png");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/assets/favicon.ico");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/assets/logo.png");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/assets/scripts.js");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/assets/styles.css");
        $this->assertDirectoryExists("{$this->pathDestination}/v1.0.0/images");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/images/example.png");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/images/logo.png");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/index.html");
        $this->assertFileExists("{$this->pathDestination}/v1.0.0/page.html");

        $this->assertDirectoryExists("{$this->pathDestination}/master/assets");
        $this->assertFileExists("{$this->pathDestination}/master/assets/apple-touch-icon-precomposed.png");
        $this->assertFileExists("{$this->pathDestination}/master/assets/favicon.ico");
        $this->assertFileExists("{$this->pathDestination}/master/assets/logo.png");
        $this->assertFileExists("{$this->pathDestination}/master/assets/scripts.js");
        $this->assertFileExists("{$this->pathDestination}/master/assets/styles.css");
        $this->assertFileExists("{$this->pathDestination}/master/page.html");
    }
}