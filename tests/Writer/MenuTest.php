<?php
declare(strict_types=1);

namespace Tests\Writer;

use Exception;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Loader;
use MarkHelp\Writer\Page;
use Tests\TestCase;

class MenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function toDirectorySingleRelease()
    {
        $projectPath = $this->normalizePath("{$this->pathReleases}/v4.0.0");
        $loader = new Loader();
        $loader->fromLocalDirectory($projectPath);

        $markdown = new File($projectPath, 'index.md');
        $markdown->setType(File::TYPE_MARKDOWN);

        $page = new Page($loader, '_', $markdown);
        $page->toDirectory($this->pathDestination);

        // o seletor só pode ser desenhado se existir mais de um release
        $renderedFile = $this->normalizePath("{$this->pathDestination}/index.html");
        $this->assertFileExists($renderedFile);
        
        $this->markTestIncomplete();
    }
}