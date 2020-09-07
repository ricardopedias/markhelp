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
    /** @test */
    public function constructException()
    {
        $this->expectException(Exception::class);

        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");

        $markdown = new File($projectPath, 'index.md');
        $this->assertNotEquals(File::TYPE_MARKDOWN, $markdown->type());

        new Page(new Loader(), 'v1.0.0', $markdown);
    }

    /** @test */
    public function generateHtml()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function toDirectory()
    {
        $this->assertTrue(true);
    }

    
}