<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\Loader;
use Tests\TestCase;

class LoaderLocalHomeAndMenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function withMenu()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader->fromLocalDirectory($projectPath);

        $this->assertNotNull($loader->menuConfig());
    }

    /** @test */
    public function withoutMenu()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v3.0.0");
        $loader->fromLocalDirectory($projectPath);

        $this->assertNull($loader->menuConfig());
    }
}