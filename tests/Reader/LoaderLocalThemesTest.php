<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\Files\Asset;
use MarkHelp\Reader\Files\Image;
use MarkHelp\Reader\Files\Markdown;
use MarkHelp\Reader\Loader;
use Tests\TestCase;

class LoaderLocalThemesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function themeDefault()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader->fromLocalDirectory($projectPath);

        $files = $loader->theme()->files(true);
        $this->assertEquals([
            $this->normalizePath("{$this->pathDefaultTheme}/assets/apple-touch-icon-precomposed.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/favicon.ico"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/logo.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/scripts.js"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/styles.css"),
        ], $files);

        $objects = $loader->theme()->files();
        $this->assertInstanceOf(Asset::class, $objects[0]);
        $this->assertInstanceOf(Asset::class, $objects[1]);
        $this->assertInstanceOf(Asset::class, $objects[2]);
        $this->assertInstanceOf(Asset::class, $objects[3]);
        $this->assertInstanceOf(Asset::class, $objects[4]);
    }

    /** @test */
    public function themeCustom()
    {
        $loader = new Loader();

        $themePath = $this->normalizePath("{$this->pathThemes}/with-templates");
        $loader->setConfig('path_theme', $themePath);

        $projectPath = $this->normalizePath("{$this->pathReleases}/v2.0.0");
        $loader->fromLocalDirectory($projectPath);

        $files = $loader->theme()->files(true);
        $this->assertEquals([
            $this->normalizePath("{$this->pathThemes}/with-templates/assets/logo.png"),
            $this->normalizePath("{$this->pathThemes}/with-templates/assets/scripts.js"),
            $this->normalizePath("{$this->pathThemes}/with-templates/assets/styles.css"),
        ], $files);

        $objects = $loader->theme()->files();
        $this->assertInstanceOf(Asset::class, $objects[0]);
        $this->assertInstanceOf(Asset::class, $objects[1]);
        $this->assertInstanceOf(Asset::class, $objects[2]);
    }

    /** @test */
    public function themeCustomWithoutTemplates()
    {
        $loader = new Loader();

        $themePath = $this->normalizePath("{$this->pathThemes}/without-templates");
        $loader->setConfig('path_theme', $themePath);

        $projectPath = $this->normalizePath("{$this->pathReleases}/v2.0.0");
        $loader->fromLocalDirectory($projectPath);

        $this->assertEquals($this->normalizePath("{$this->pathDefaultTheme}/templates"), $loader->templatesPath());
    }

    /** @test */
    public function themeCustomWithTemplates()
    {
        $loader = new Loader();

        $themePath = $this->normalizePath("{$this->pathThemes}/with-templates");
        $loader->setConfig('path_theme', $themePath);

        $projectPath = $this->normalizePath("{$this->pathReleases}/v2.0.0");
        $loader->fromLocalDirectory($projectPath);

        $this->assertEquals($this->normalizePath("{$this->pathThemes}/with-templates/templates"), $loader->templatesPath());
    }
}