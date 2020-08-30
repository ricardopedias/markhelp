<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\Files\Asset;
use MarkHelp\Reader\Files\File;
use MarkHelp\Reader\Files\HomePage;
use MarkHelp\Reader\Files\Image;
use MarkHelp\Reader\Files\Markdown;
use MarkHelp\Reader\Files\Menu;
use MarkHelp\Reader\Loader;
use Tests\TestCase;

class LoaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function setAndRetrieveSettings()
    {
        $loader = new Loader();
        $loader->setConfig('path_project', '/teste/dir');
        $this->assertEquals('/teste/dir', $loader->config('path_project'));
    }
}