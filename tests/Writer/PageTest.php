<?php
declare(strict_types=1);

namespace Tests\Writer;

use Exception;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Loader;
use MarkHelp\Writer\Page;
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
    public function toDirectorySingleRelease()
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
    }

    /** @test */
    public function toDirectoryMultiReleases()
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
    }

    private function createInstanceOfPage(string $releaseName, string $projectPath, string $filePath, string $logoPath = '/images/logo.png')
    {
        $projectPath = $this->normalizePath($projectPath);
        $loader = new Loader();
        $loader->setConfig('project_logo', $logoPath);
        $loader->fromLocalDirectory($projectPath);
        $markdown = new File($projectPath, $filePath);
        $markdown->setType(File::TYPE_MARKDOWN);
        return new Page($loader, $releaseName, $markdown);
    }

    /** @test */
    public function resolveProjectLogoUrlFromProjectSingleRelease()
    {
        // O release v1.0.0 possui um images/logo.png
        $page = $this->createInstanceOfPage('_', "{$this->pathReleases}/v1.0.0", 'page.md');

        $reflection = new \ReflectionClass(Page::class);
        $reflectionMethod = $reflection->getMethod('resolveProjectLogoUrl');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($page);
        $this->assertEquals('images/logo.png', $result);
    }

    /** @test */
    public function resolveProjectLogoUrlFromProjectMultiReleases()
    {
        // O release v1.0.0 possui um images/logo.png
        $page = $this->createInstanceOfPage('v1.0.0', "{$this->pathReleases}", 'page.md');

        $reflection = new \ReflectionClass(Page::class);
        $reflectionMethod = $reflection->getMethod('resolveProjectLogoUrl');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($page);
        $this->assertEquals('images/logo.png', $result);
    }

    /** @test */
    public function resolveProjectLogoUrlFromTheme()
    {
        // O release v2.0.0 NÃO possui um images/logo.png
        $page = $this->createInstanceOfPage('_', "{$this->pathReleases}/v2.0.0", 'page.md');

        $reflection = new \ReflectionClass(Page::class);
        $reflectionMethod = $reflection->getMethod('resolveProjectLogoUrl');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($page);
        $this->assertEquals('assets/logo.png', $result);
    }

    /** @test */
    public function resolveProjectCustomLogoUrlFromProject()
    {
        // O release v3.0.0 possui um test/logo.jpg
        $page = $this->createInstanceOfPage('_', "{$this->pathReleases}/v3.0.0", 'page.md', '/test/logo.jpg');

        $reflection = new \ReflectionClass(Page::class);
        $reflectionMethod = $reflection->getMethod('resolveProjectLogoUrl');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($page);
        $this->assertEquals('test/logo.jpg', $result);
    }

    /** @test */
    public function resolveProjectCustomLogoUrlFromTheme()
    {
        // O release v3.0.0 NÃO possui um not-exixt/logo.jpg
        $page = $this->createInstanceOfPage('_', "{$this->pathReleases}/v3.0.0", 'page.md', '{{project}}/not-exixt/logo.jpg');

        $reflection = new \ReflectionClass(Page::class);
        $reflectionMethod = $reflection->getMethod('resolveProjectLogoUrl');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($page);
        $this->assertEquals('assets/logo.png', $result);
    }

    /** @test */
    public function generateHtmlSingleRelease()
    {
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
        $loader = new Loader();
        $loader->fromLocalDirectory($projectPath);

        $markdown = new File($projectPath, 'page.md');
        $markdown->setType(File::TYPE_MARKDOWN);

        $page = new Page($loader, '_', $markdown);
        $renderedHtml = $page->generateHtml();

        $this->assertStringContainsString('./images/logo.png', $renderedHtml);

        $this->assertStringContainsString('Pagina 1', $renderedHtml);
        $this->assertStringNotContainsString('<select class="version-select">', $renderedHtml);
    }

    /** @test */
    public function generateHtmlMultiReleases()
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
        $renderedHtml = $page->generateHtml();

        // $this->assertStringContainsString('./images/logo.png', $renderedHtml);

        // <img src="./images/logo.png" width="50" height="40" alt="Mark Help - Gerador de documentação">

        $this->assertStringContainsString('Pagina 1', $renderedHtml);
        $this->assertStringContainsString('<option value="../1/page.html">1</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../1.0/page.html">1.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../1.0.0/page.html">1.0.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../111.0.0/page.html">111.0.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../111.111.0/page.html">111.111.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../111.111.111/page.html">111.111.111</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../master/page.html">master</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v1/page.html">v1</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v1.0/page.html">v1.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v1.0.0/index.html" selected>v1.0.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v111.0.0/page.html">v111.0.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v111.111.0/page.html">v111.111.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v111.111.111/page.html">v111.111.111</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v2.0.0/page.html">v2.0.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v3.0.0/page.html">v3.0.0</option>', $renderedHtml);
        $this->assertStringContainsString('<option value="../v4.0.0/index.html">v4.0.0</option>', $renderedHtml);
    }
}