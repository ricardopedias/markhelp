<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\Files\Asset;
use MarkHelp\Reader\Files\File;
use MarkHelp\Reader\Files\Image;
use MarkHelp\Reader\Files\Markdown;
use MarkHelp\Reader\Loader;
use Tests\TestCase;

class LoaderRemoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function fromRemoteUrl()
    {
        $loader = new Loader();
        $loader->fromRemoteUrl(
            'https://github.com/ricardopedias/markhelp-test-repo.git', 
            $this->pathDestination
        );

        $this->assertNotNull($loader->menuConfig());

        $clonedRelease1 = "{$this->pathDestination}/markhelp-test-repo/v1.0.0";
        $clonedRelease2 = "{$this->pathDestination}/markhelp-test-repo/v2.0.0";
        $clonedRelease3 = "{$this->pathDestination}/markhelp-test-repo/v3.0.0";

        $releases = $loader->releases();

        $this->assertArrayHasKey('v1.0.0', $releases);
        $this->assertArrayHasKey('v2.0.0', $releases);
        $this->assertArrayHasKey('v3.0.0', $releases);

        // v1.0.0

        $this->assertEquals([
            $this->normalizePath("{$clonedRelease1}/como-ajudar.md"),
            $this->normalizePath("{$clonedRelease1}/configuracoes.md"),
            $this->normalizePath("{$clonedRelease1}/images/menu-lateral.png"),
            $this->normalizePath("{$clonedRelease1}/images/screenshot.png"),
            $this->normalizePath("{$clonedRelease1}/index.md"),
            $this->normalizePath("{$clonedRelease1}/instalando.md"),
            $this->normalizePath("{$clonedRelease1}/utilizar-como-biblioteca.md"),
            $this->normalizePath("{$clonedRelease1}/utilizar-no-terminal.md"),
        ], $releases['v1.0.0']->files(true));

        $objects = $releases['v1.0.0']->files();
        $this->assertInstanceOf(Markdown::class, $objects[0]);
        $this->assertInstanceOf(Markdown::class, $objects[1]);
        $this->assertInstanceOf(Image::class, $objects[2]);
        $this->assertInstanceOf(Image::class, $objects[3]);
        $this->assertInstanceOf(Markdown::class, $objects[4]);
        $this->assertInstanceOf(Markdown::class, $objects[5]);
        $this->assertInstanceOf(Markdown::class, $objects[6]);

        // v2.0.0

        $this->assertEquals([
            $this->normalizePath("{$clonedRelease2}/como-ajudar.md"),
            $this->normalizePath("{$clonedRelease2}/configuracoes.md"),
            $this->normalizePath("{$clonedRelease2}/images/menu-lateral.png"),
            $this->normalizePath("{$clonedRelease2}/images/screenshot.png"),
            $this->normalizePath("{$clonedRelease2}/index.md"),
            $this->normalizePath("{$clonedRelease2}/instalando.md"),
            $this->normalizePath("{$clonedRelease2}/utilizar-como-biblioteca.md"),
            $this->normalizePath("{$clonedRelease2}/utilizar-no-terminal.md"),
        ], $releases['v2.0.0']->files(true));

        // v3.0.0

        $this->assertEquals([
            $this->normalizePath("{$clonedRelease3}/como-ajudar.md"),
            $this->normalizePath("{$clonedRelease3}/configuracoes.md"),
            $this->normalizePath("{$clonedRelease3}/images/menu-lateral.png"),
            $this->normalizePath("{$clonedRelease3}/images/screenshot.png"),
            $this->normalizePath("{$clonedRelease3}/index.md"),
            $this->normalizePath("{$clonedRelease3}/instalando.md"),
            $this->normalizePath("{$clonedRelease3}/utilizar-como-biblioteca.md"),
            $this->normalizePath("{$clonedRelease3}/utilizar-no-terminal.md"),
        ], $releases['v3.0.0']->files(true));

        // tema

        $theme = $loader->theme()->files(true);
        $this->assertEquals([
            $this->normalizePath("{$this->pathDefaultTheme}/assets/apple-touch-icon-precomposed.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/favicon.ico"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/logo.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/scripts.js"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/styles.css"),
        ], $loader->theme()->files(true));

        $theme = $loader->theme()->files();
        $this->assertInstanceOf(Asset::class, $theme[0]);
        $this->assertInstanceOf(Asset::class, $theme[1]);
        $this->assertInstanceOf(Asset::class, $theme[2]);
        $this->assertInstanceOf(Asset::class, $theme[3]);
        $this->assertInstanceOf(Asset::class, $theme[4]);
    }
}