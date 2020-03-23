<?php
declare(strict_types=1);

namespace Tests;

use Exception;
use MarkHelp\App;
use MarkHelp\MarkHelp;

class MarkHelpTest extends TestCase
{
    /**
     * @test
     */
    public function renderDefaultConfigs()
    {
        $app = new MarkHelp($this->pathRootComplete);
        $app->saveTo($this->pathDestination);
        
        $this->assertDirectoryExists("{$this->pathDestination}/assets");
        $this->assertDirectoryExists("{$this->pathDestination}/avançado");
        $this->assertDirectoryExists("{$this->pathDestination}/o_básico");
        $this->assertFileExists("{$this->pathDestination}/assets/styles.css");
        $this->assertFileExists("{$this->pathDestination}/index.html");
        $this->assertFileExists("{$this->pathDestination}/page-one.html");
        $this->assertFileExists("{$this->pathDestination}/page-two.html");
    }

    /**
     * @test
     */
    public function renderConfigFile()
    {
        $app = new MarkHelp($this->pathRootComplete);
        $app->loadConfigFrom($this->pathExternal . DIRECTORY_SEPARATOR . "config.json");
        $app->saveTo($this->pathDestination);
        
        $this->assertDirectoryExists("{$this->pathDestination}/assets");
        $this->assertDirectoryExists("{$this->pathDestination}/avançado");
        $this->assertDirectoryExists("{$this->pathDestination}/o_básico");
        $this->assertFileExists("{$this->pathDestination}/assets/styles.css");
        $this->assertFileExists("{$this->pathDestination}/index.html");
        $this->assertFileExists("{$this->pathDestination}/page-one.html");
        $this->assertFileExists("{$this->pathDestination}/page-two.html");
    }

    /**
     * @test
     */
    public function renderRepositoryDefaultConfigs()
    {
        $app = new MarkHelp('https://github.com/ricardopedias/markhelp.git');
        $app->saveTo($this->pathDestination);

        $this->assertDirectoryExists("{$this->pathDestination}/markhelp/master");
        $this->assertDirectoryNotExists("{$this->pathDestination}/markhelp/v1.0.0");
        $this->assertDirectoryNotExists("{$this->pathDestination}/markhelp/v2.0.0");
        $this->assertDirectoryNotExists("{$this->pathDestination}/markhelp/v3.0.0");
    }

    /**
     * @test
     */
    public function renderRepositoryCustomConfigs()
    {
        $app = new MarkHelp('https://github.com/ricardopedias/markhelp.git');
        $app->loadConfigFrom($this->pathExternal . DIRECTORY_SEPARATOR . "config.json");
        $app->saveTo($this->pathDestination);

        $this->assertDirectoryExists("{$this->pathDestination}/markhelp/master");
        $this->assertDirectoryExists("{$this->pathDestination}/markhelp/v1.0.0");
        $this->assertDirectoryExists("{$this->pathDestination}/markhelp/v2.0.0");
        $this->assertDirectoryExists("{$this->pathDestination}/markhelp/v3.0.0");
    }
}