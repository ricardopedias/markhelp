<?php
declare(strict_types=1);

namespace Tests;

use Exception;
use MarkHelp\App;
use MarkHelp\MarkHelp;

class MarkHelpTest extends TestCase
{
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
    //     $this->assertFileExists("{$this->pathDestination}/index.html");
    //     $this->assertFileExists("{$this->pathDestination}/page-one.html");
    //     $this->assertFileExists("{$this->pathDestination}/page-two.html");
    // }

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

    // /**
    //  * @test
    //  */
    // public function renderConfigFileSintaxException()
    // {
    //     $this->expectException(Exception::class);

    //     $app = new MarkHelp($this->pathRootComplete);
    //     $app->loadConfigFrom($this->pathExternal . DIRECTORY_SEPARATOR . "config-sintax.json");
    //     $app->saveTo($this->pathDestination);
    // }

    // /**
    //  * @test
    //  */
    // public function renderConfigFileException()
    // {
    //     $this->expectException(Exception::class);

    //     $app = new MarkHelp($this->pathRootComplete);
    //     $app->loadConfigFrom($this->pathExternal . DIRECTORY_SEPARATOR . "config-invalid.json");
    //     $app->saveTo($this->pathDestination);
    // }
}