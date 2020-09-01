<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\File;
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
        $this->assertEquals(File::TYPE_ASSET, $objects[0]->type());
        $this->assertEquals(File::TYPE_ASSET, $objects[1]->type());
        $this->assertEquals(File::TYPE_ASSET, $objects[2]->type());
        $this->assertEquals(File::TYPE_ASSET, $objects[3]->type());
        $this->assertEquals(File::TYPE_ASSET, $objects[4]->type());
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
        $this->assertEquals(File::TYPE_ASSET, $objects[0]->type());
        $this->assertEquals(File::TYPE_ASSET, $objects[1]->type());
        $this->assertEquals(File::TYPE_ASSET, $objects[2]->type());
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