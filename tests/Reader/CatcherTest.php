<?php

declare(strict_types=1);

namespace Tests\Reader;

use MarkHelp\Reader\Git\Catcher;
use Tests\TestCase;

class CatcherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }
    
    /** @test */
    public function cloneOne()
    {
        $gotcha = new Catcher('https://github.com/ricardopedias/markhelp-test-repo.git');
        $gotcha->addRelease('v1.0.0');
        $gotcha->cloneTo($this->pathDestination);

        $repoPath = $this->pathDestination . DIRECTORY_SEPARATOR . 'markhelp-test-repo';
        $this->assertDirectoryExists($repoPath);
        $this->assertDirectoryExists($repoPath . DIRECTORY_SEPARATOR . 'v1.0.0');
        $this->assertDirectoryDoesNotExist($repoPath . DIRECTORY_SEPARATOR . 'v2.0.0');
        $this->assertDirectoryDoesNotExist($repoPath . DIRECTORY_SEPARATOR . 'v3.0.0');
    }

    /** @test */
    public function cloneAll()
    {
        $gotcha = new Catcher('https://github.com/ricardopedias/markhelp-test-repo.git');
        $gotcha->cloneTo($this->pathDestination);

        $repoPath = $this->pathDestination . DIRECTORY_SEPARATOR . 'markhelp-test-repo';
        $this->assertDirectoryExists($repoPath);
        $this->assertDirectoryExists($repoPath . DIRECTORY_SEPARATOR . 'v1.0.0');
        $this->assertDirectoryExists($repoPath . DIRECTORY_SEPARATOR . 'v2.0.0');
        $this->assertDirectoryExists($repoPath . DIRECTORY_SEPARATOR . 'v3.0.0');
    }

    /** @test */
    public function deleteClonedContents()
    {
        $gotcha = new Catcher('https://github.com/ricardopedias/markhelp-test-repo.git');
        $gotcha->cloneTo($this->pathDestination);

        $clonedPath = $this->pathDestination . DIRECTORY_SEPARATOR . 'markhelp-test-repo-clone';
        $this->assertDirectoryDoesNotExist($clonedPath);
        $this->assertFileDoesNotExist($clonedPath . DIRECTORY_SEPARATOR . 'readme.md');
    }
    
}