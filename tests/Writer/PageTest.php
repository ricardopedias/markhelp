<?php
declare(strict_types=1);

namespace Tests\Writer;

use Exception;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Loader;
use MarkHelp\Writer\Page;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class PageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function constructException()
    {
        $this->expectException(Exception::class);

        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $markdown = new File($projectPath, 'anything.md');
        $this->assertNotEquals(File::TYPE_MARKDOWN, $markdown->type());
        new Page(new Loader(), 'v1.0.0', $markdown);
    }

    /** @test */
    public function generateHtml()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function renderManyReleases()
    {
        // inicia o carregador de projetos
        $projectPath = $this->normalizePath("{$this->pathReleases}");
        $loader = new Loader();
        $loader->fromLocalDirectory($projectPath);

        // intancia um arquivo markdown individual
        $markdown = new File($projectPath, 'v1.0.0/page.md');
        $markdown->setType(File::TYPE_MARKDOWN);

        // renderiza o arquivo para html
        $page = new Page($loader, 'v1.0.0', $markdown);
        $page->toDirectory($this->pathDestination);

        $renderedFile = $this->normalizePath("{$this->pathDestination}/v1.0.0/page.html");
        $this->assertFileExists($renderedFile);

        $renderedFile = file_get_contents($renderedFile);
        $this->assertStringContainsString('Pagina 1', $renderedFile);
        $this->assertStringContainsString('<option value="../1/page.html">1</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../1.0/page.html">1.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../1.0.0/page.html">1.0.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../111.0.0/page.html">111.0.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../111.111.0/page.html">111.111.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../111.111.111/page.html">111.111.111</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../master/page.html">master</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v1/page.html">v1</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v1.0/page.html">v1.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v1.0.0/index.html" selected>v1.0.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v111.0.0/page.html">v111.0.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v111.111.0/page.html">v111.111.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v111.111.111/page.html">v111.111.111</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v2.0.0/page.html">v2.0.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v3.0.0/page.html">v3.0.0</option>', $renderedFile);
        $this->assertStringContainsString('<option value="../v4.0.0/index.html">v4.0.0</option>', $renderedFile);
    }

    /** @test */
    public function renderSingleRelease()
    {
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader = new Loader();
        $loader->fromLocalDirectory($projectPath);

        $markdown = new File($projectPath, 'page.md');
        $markdown->setType(File::TYPE_MARKDOWN);

        $page = new Page($loader, '_', $markdown);
        $page->toDirectory($this->pathDestination);

        // o seletor só pode ser desenhado se existir mais de um release
        $renderedFile = $this->normalizePath("{$this->pathDestination}/page.html");
        $this->assertFileExists($renderedFile);        
        
        $renderedFile = file_get_contents($renderedFile);
        $this->assertStringContainsString('Pagina 1', $renderedFile);
        $this->assertStringNotContainsString('<select class="version-select">', $renderedFile);
    }
}