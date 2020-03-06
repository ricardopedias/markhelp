<?php
declare(strict_types=1);

namespace Tests\App;

use MarkHelp\App\Filesystem;
use Tests\TestCase;

class FilesystemTest extends TestCase
{
    /**
     * @test
     */
    public function create()
    {
        $filesystem = new Filesystem;
        $filesystem->mount('origin', $this->pathRootComplete);
        $filesystem->mount('destination', $this->pathDestination);

        $list = $filesystem->listContents('origin://.', true);
        $this->assertIsArray($list);

        $list = $filesystem->listContents('destination://.', true);
        $this->assertIsArray($list);
    }
}