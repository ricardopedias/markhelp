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
    public function generateHtml()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function renderWithRelease()
    {
        $projectPath = $this->normalizePath("{$this->pathReleases}");
        $loader = new Loader();
        $loader->fromLocalDirectory($projectPath);

        $markdown = new File($projectPath, 'v1.0.0/page.md');
        $markdown->setType(File::TYPE_MARKDOWN);

        $page = new Page($loader, 'v1.0.0', $markdown);
        $page->toDirectory($this->pathDestination);

        $renderedFile = $this->normalizePath("{$this->pathDestination}/v1.0.0/page.html");
        $this->assertStringContainsString('Pagina 1', $renderedFile);

        // <option value="../1/page.html">1</option>
        //                                 <option value="../1.0/page.html">1.0</option>
        //                                 <option value="../1.0.0/page.html">1.0.0</option>
        //                                 <option value="../111.0.0/page.html">111.0.0</option>
        //                                 <option value="../111.111.0/page.html">111.111.0</option>
        //                                 <option value="../111.111.111/page.html">111.111.111</option>
        //                                 <option value="../master/page.html">master</option>
    }

    // /** @test */
    // public function renderWithoutReleases()
    // {
    //     $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");
    //     $loader = new Loader();
    //     $loader->fromLocalDirectory($projectPath);

    //     $markdown = new File($projectPath, 'page.md');
    //     $markdown->setType(File::TYPE_MARKDOWN);

    //     $page = new Page($loader, '_', $markdown);
    //     $page->toDirectory($this->pathDestination);
    //     die;

    //     $this->assertTrue(true);
    // }

    
}