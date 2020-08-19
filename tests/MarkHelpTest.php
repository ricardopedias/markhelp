<?php
declare(strict_types=1);

namespace Tests;

use MarkHelp\MarkHelp;

class MarkHelpTest extends TestCase
{
    /**
     * @test
     */
    public function canBeGitUrl()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function normalizeValue()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function setConfig()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function config()
    {
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function loadConfigFrom()
    {
        $this->assertTrue(true);
    }
    
    // /**
    //  * @test
    //  */
    // public function renderDefaultConfigs()
    // {
    //     $app = new MarkHelp($this->pathRootComplete);
    //     $app->saveTo($this->pathDestination);
        
    //     $this->assertDirectoryExists("{$this->pathDestination}/assets");
    //     $this->assertDirectoryExists("{$this->pathDestination}/avançado");
    //     $this->assertDirectoryExists("{$this->pathDestination}/o_básico");
    //     $this->assertFileExists("{$this->pathDestination}/assets/styles.css");
    //     $this->assertFileExists("{$this->pathDestination}/home.html"); // os arquivos 'index.md' são convertidos para 'home.html'
    //     $this->assertFileExists("{$this->pathDestination}/page-one.html");
    //     $this->assertFileExists("{$this->pathDestination}/page-two.html");
    // }

    // /**
    //  * @test
    //  */
    // public function renderConfigFile()
    // {
    //     $app = new MarkHelp($this->pathRootComplete);
    //     $app->loadConfigFrom($this->pathExternal . DIRECTORY_SEPARATOR . "config.json");
    //     $app->saveTo($this->pathDestination);
        
    //     $this->assertDirectoryExists("{$this->pathDestination}/assets");
    //     $this->assertDirectoryExists("{$this->pathDestination}/avançado");
    //     $this->assertDirectoryExists("{$this->pathDestination}/o_básico");
    //     $this->assertFileExists("{$this->pathDestination}/assets/styles.css");
    //     $this->assertFileExists("{$this->pathDestination}/home.html"); // os arquivos 'index.md' são convertidos para 'home.html'
    //     $this->assertFileExists("{$this->pathDestination}/page-one.html");
    //     $this->assertFileExists("{$this->pathDestination}/page-two.html");
    // }

    /**
     * @test
     */
    public function renderRepositoryDefaultConfigs()
    {
        $app = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $app->saveTo($this->pathDestination);

        $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/master");
        $this->assertDirectoryDoesNotExist("{$this->pathDestination}/markhelp-test-repo/v1.0.0");
        $this->assertDirectoryDoesNotExist("{$this->pathDestination}/markhelp-test-repo/v2.0.0");
        $this->assertDirectoryDoesNotExist("{$this->pathDestination}/markhelp-test-repo/v3.0.0");
    }

    /**
     * @test
     */
    public function renderRepositoryCustomConfigs()
    {
        $app = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $app->loadConfigFrom($this->pathExternal . DIRECTORY_SEPARATOR . "config.json");
        $app->saveTo($this->pathDestination);

        $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/master");
        $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/v1.0.0");
        $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/v2.0.0");
        $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/v3.0.0");
    }
}