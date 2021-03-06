<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\Loader;
use Tests\TestCase;

class LoaderLocalReleasesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function projectMultiReleases()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath($this->pathReleases);
        $loader->fromLocalDirectory($projectPath);

        $this->assertTrue($loader->hasReleases());

        $releases = $loader->releases();

        $this->assertCount(16, $releases);

        $this->assertArrayHasKey('1', $releases);
        $this->assertArrayHasKey('1.0', $releases);
        $this->assertArrayHasKey('1.0.0', $releases);
        $this->assertArrayHasKey('111.0.0', $releases);
        $this->assertArrayHasKey('111.111.0', $releases);
        $this->assertArrayHasKey('111.111.111', $releases);
        $this->assertArrayHasKey('master', $releases);
        $this->assertArrayHasKey('v1', $releases);
        $this->assertArrayHasKey('v1.0', $releases);
        $this->assertArrayHasKey('v1.0.0', $releases);
        $this->assertArrayHasKey('v111.0.0', $releases);
        $this->assertArrayHasKey('v111.111.0', $releases);
        $this->assertArrayHasKey('v111.111.111', $releases);
        $this->assertArrayHasKey('v2.0.0', $releases);
        $this->assertArrayHasKey('v3.0.0', $releases);
        $this->assertArrayHasKey('v4.0.0', $releases);

        $expectsOneFile = [
            '1', '1.0.0', '111.0.0', '111.111.0', '111.111.111', 'master',
            'v1', 'v111.0.0','v111.111.0', 'v111.111.111' 
        ];

        array_walk($expectsOneFile, function($version) use ($releases) {

            $pagePath = $this->normalizePath("{$this->pathReleases}/{$version}/page.md");
            $this->assertEquals([$pagePath], $releases[$version]->filesAsString());

            $homePath = $releases[$version]->home()->fullPath();
            $this->assertEquals($homePath, $this->normalizePath("{$this->pathReleases}/{$version}/page.md"));
        });

        $this->assertEquals([
            $this->normalizePath("{$this->pathReleases}/v1.0.0/images/example.png"),
            $this->normalizePath("{$this->pathReleases}/v1.0.0/images/logo.png"),
            $this->normalizePath("{$this->pathReleases}/v1.0.0/index.md"),
            $this->normalizePath("{$this->pathReleases}/v1.0.0/page.md"),
        ], $releases['v1.0.0']->filesAsString());

        $homePath = $releases['v1.0.0']->home()->fullPath();
        $this->assertEquals($homePath, $this->normalizePath("{$this->pathReleases}/v1.0.0/index.md"));

        $this->assertEquals([
            $this->normalizePath("{$this->pathReleases}/v2.0.0/images/example.png"),
            $this->normalizePath("{$this->pathReleases}/v2.0.0/page.md"),
            $this->normalizePath("{$this->pathReleases}/v2.0.0/sub/subpage.md"),
        ], $releases['v2.0.0']->filesAsString());

        $homePath = $releases['v2.0.0']->home()->fullPath();
        $this->assertEquals($homePath, $this->normalizePath("{$this->pathReleases}/v2.0.0/page.md"));

        $this->assertEquals([
            $this->normalizePath("{$this->pathReleases}/v3.0.0/images/example.png"),
            $this->normalizePath("{$this->pathReleases}/v3.0.0/page.md"),
            $this->normalizePath("{$this->pathReleases}/v3.0.0/test/logo.jpg"),
        ], $releases['v3.0.0']->filesAsString());

        $homePath = $releases['v3.0.0']->home()->fullPath();
        $this->assertEquals($homePath, $this->normalizePath("{$this->pathReleases}/v3.0.0/page.md"));

        $this->assertEquals([
            $this->normalizePath("{$this->pathDefaultTheme}/assets/apple-touch-icon-precomposed.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/favicon.ico"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/logo.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/scripts.js"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/styles.css"),
        ], $loader->theme()->filesAsString());
    }

    /** @test */
    public function projectSingleRelease()
    {
        $loader = new Loader();
        
        // Diretório do projeto sem releases, ou seja, ele é o próprio release
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader->fromLocalDirectory($projectPath);

        $this->assertFalse($loader->hasReleases());

        $releases = $loader->releases();

        // Apenas um release encontrado
        $this->assertArrayHasKey('_', $releases);
        $this->assertCount(1, $releases);

        $this->assertEquals([
            $this->normalizePath("{$this->pathReleases}/v1.0.0/images/example.png"),
            $this->normalizePath("{$this->pathReleases}/v1.0.0/images/logo.png"),
            $this->normalizePath("{$this->pathReleases}/v1.0.0/index.md"),
            $this->normalizePath("{$this->pathReleases}/v1.0.0/page.md"),
        ], $releases['_']->filesAsString());

        $homePath = $releases['_']->home()->fullPath();
        $this->assertEquals($homePath, $this->normalizePath("{$this->pathReleases}/v1.0.0/index.md"));
    }
}