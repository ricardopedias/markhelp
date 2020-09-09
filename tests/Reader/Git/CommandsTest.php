<?php

declare(strict_types=1);

namespace Tests\Reader\Git;

use MarkHelp\Reader\Git\Commands;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function commandRun()
    {
        $commands = new Commands();
        $result = $commands->run(['echo "teste"']);
        $this->assertEquals('teste', $result);
    }

    /** @test */
    public function commandRunSeparated()
    {
        $commands = new Commands();
        $result = $commands->run(['echo', 'teste']);
        $this->assertEquals('teste', $result);
    }

    /** @test */
    public function commandIsValidRemote()
    {
        $commands = new Commands();
        $result = $commands->isValidRemote('https://github.com/ricardopedias/markhelp-test-repo.git');
        $this->assertTrue($result);

        $commands = new Commands();
        $result = $commands->isValidRemote('https://github.com/ricardopedias/markhelp-test-repo-invalid.git');
        $this->assertFalse($result);
    }

    /** @test */
    public function commandClone()
    {
        $clonePath = $this->normalizePath("{$this->pathDestination}/clone");

        $commands = new Commands();
        $commands->clone('https://github.com/ricardopedias/markhelp-test-repo.git', $clonePath);

        $this->assertDirectoryExists($clonePath);
        $this->assertFileExists($clonePath . DIRECTORY_SEPARATOR . 'readme.md');
    }

    /** @test */
    public function commandGetReleases()
    {
        $clonePath = $this->normalizePath("{$this->pathDestination}/clone");

        $commands = new Commands();
        $commands->clone('https://github.com/ricardopedias/markhelp-test-repo.git', $clonePath);
        $list = $commands->getReleases($clonePath);
        $this->assertEquals(['v1.0.0', 'v2.0.0', 'v3.0.0'], $list);
    }

    /** @test */
    public function commandCheckoutRelease()
    {
        $clonePath = $this->normalizePath("{$this->pathDestination}/clone");

        $commands = new Commands();
        $commands->clone('https://github.com/ricardopedias/markhelp-test-repo.git', $clonePath);
        
        $result = $commands->checkoutRelease($clonePath, 'v2.0.0');
        $this->assertEquals('HEAD is now at 4caa560 Adicionada modificação para testar a versão 2', $result);
    }
}