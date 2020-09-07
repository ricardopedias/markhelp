<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\File;
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
        ], $releases['v1.0.0']->filesAsString());

        $objects = $releases['v1.0.0']->files();
        $this->assertEquals(File::TYPE_MARKDOWN, $objects[0]->type());
        $this->assertEquals(File::TYPE_MARKDOWN, $objects[1]->type());
        $this->assertEquals(File::TYPE_IMAGE, $objects[2]->type());
        $this->assertEquals(File::TYPE_IMAGE, $objects[3]->type());
        $this->assertEquals(File::TYPE_MARKDOWN, $objects[4]->type());
        $this->assertEquals(File::TYPE_MARKDOWN, $objects[5]->type());
        $this->assertEquals(File::TYPE_MARKDOWN, $objects[6]->type());

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
        ], $releases['v2.0.0']->filesAsString());

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
        ], $releases['v3.0.0']->filesAsString());

        // tema
        $this->assertEquals([
            $this->normalizePath("{$this->pathDefaultTheme}/assets/apple-touch-icon-precomposed.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/favicon.ico"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/logo.png"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/scripts.js"),
            $this->normalizePath("{$this->pathDefaultTheme}/assets/styles.css"),
        ], $loader->theme()->filesAsString());

        $theme = $loader->theme()->files();
        $this->assertEquals(File::TYPE_ASSET, $theme[0]->type());
        $this->assertEquals(File::TYPE_ASSET, $theme[1]->type());
        $this->assertEquals(File::TYPE_ASSET, $theme[2]->type());
        $this->assertEquals(File::TYPE_ASSET, $theme[3]->type());
        $this->assertEquals(File::TYPE_ASSET, $theme[4]->type());
    }
}