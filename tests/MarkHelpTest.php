<?php
declare(strict_types=1);

namespace Tests;

use Exception;
use MarkHelp\MarkHelp;

class MarkHelpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function canBeGitUrl()
    {
        $reflection = new \ReflectionClass(MarkHelp::class);
        $reflectionMethod = $reflection->getMethod('canBeGitUrl');
        $reflectionMethod->setAccessible(true);
        
        $markHelp = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $result = $reflectionMethod->invoke($markHelp);
        $this->assertTrue($result);

        $markHelp = new MarkHelp('https://github.com');
        $result = $reflectionMethod->invoke($markHelp);
        $this->assertFalse($result);
    }

    /** @test */
    public function configInvalid()
    {
        $this->expectException(Exception::class);

        $markHelp = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $markHelp->config("invalid_param");
    }

    /** @test */
    public function config()
    {
        $markHelp = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $markHelp->setConfig("copy_url", 'http://www.ricardopedias.com.br');
        $this->assertEquals('http://www.ricardopedias.com.br', $markHelp->config("copy_url"));
    }

    /** @test */
    public function loadConfigFrom()
    {
        $configFile = $this->normalizePath("{$this->pathConfigurations}/config.json");

        $markHelp = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $markHelp->loadConfigFrom($configFile);

        $this->assertEquals('', $markHelp->config("path_theme"));
        $this->assertEquals('http://www.ricardopedias.com.br', $markHelp->config("clone_url"));
        $this->assertEquals('/docs', $markHelp->config("clone_directory"));
        $this->assertEquals('v0.0.05, v1.66.5555', $markHelp->config("clone_tags"));
        $this->assertEquals('Ricardo Pereira', $markHelp->config("copy_name"));
        $this->assertEquals('https://github.com/ricardopedias', $markHelp->config("copy_url"));

        $this->assertEquals("Documentação Fácil", $markHelp->config("project_name"));
        $this->assertEquals("Fazedor de documentos com buniteza", $markHelp->config("project_slogan"));
        $this->assertEquals("true", $markHelp->config("project_fork"));
        $this->assertEquals("Este é um projeto bem legal", $markHelp->config("project_description"));
        $this->assertEquals("enabled", $markHelp->config("project_logo_status"));
        $this->assertEquals("{{project}}/logo.png", $markHelp->config("project_logo"));
    }

    /** @test */
    public function loadConfigFromInvalidFormat()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Parameter generate_phpindex does not contain a valid value");

        $configFile = $this->normalizePath("{$this->pathConfigurations}/config-invalid.json");
        $markHelp = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $markHelp->loadConfigFrom($configFile);
    }

    /** @test */
    public function loadConfigFromInvalidSintax()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches("/The config file does not contain a json.*/");
        
        $configFile = $this->normalizePath("{$this->pathConfigurations}/config-sintax.json");
        $markHelp = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $markHelp->loadConfigFrom($configFile);
    }

    /** @test */
    public function loadFromGitSaveTo()
    {
        $markHelp = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
        $markHelp->saveTo($this->pathDestination);

        $release1 = $this->normalizePath("{$this->pathDestination}/v1.0.0");
        $release2 = $this->normalizePath("{$this->pathDestination}/v2.0.0");
        $release3 = $this->normalizePath("{$this->pathDestination}/v3.0.0");

        $this->assertDirectoryExists($release1);
        $this->assertDirectoryExists($release2);
        $this->assertDirectoryExists($release3);
    }

    /** @test */
    public function loadFromLocalSaveTo()
    {
        $markHelp = new MarkHelp($this->pathReleases);
        $markHelp->saveTo($this->pathDestination);

        $release1 = $this->normalizePath("{$this->pathDestination}/1");
        $release10 = $this->normalizePath("{$this->pathDestination}/1.0");
        $release100 = $this->normalizePath("{$this->pathDestination}/1.0.0");
        $releaseMaster = $this->normalizePath("{$this->pathDestination}/master");
        $releaseV1 = $this->normalizePath("{$this->pathDestination}/v1.0.0");
        $releaseV2 = $this->normalizePath("{$this->pathDestination}/v2.0.0");
        $releaseV3 = $this->normalizePath("{$this->pathDestination}/v3.0.0");

        $this->assertDirectoryExists($release1);
        $this->assertDirectoryExists($release10);
        $this->assertDirectoryExists($release100);
        $this->assertDirectoryExists($releaseMaster);
        $this->assertDirectoryExists($releaseV1);
        $this->assertDirectoryExists($releaseV2);
        $this->assertDirectoryExists($releaseV3);
    }

    // /**
    //  * @test
    //  */
    // public function setConfig()
    // {
    //     $this->assertTrue(true);
    // }

    // /**
    //  * @test
    //  */
    // public function config()
    // {
    //     $this->assertTrue(true);
    // }
    // /**
    //  * @test
    //  */
    // public function loadConfigFrom()
    // {
    //     $this->assertTrue(true);
    // }
    
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

    // /**
    //  * @test
    //  */
    // public function renderRepositoryDefaultConfigs()
    // {
    //     $app = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
    //     $app->saveTo($this->pathDestination);

    //     $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/master");
    //     $this->assertDirectoryDoesNotExist("{$this->pathDestination}/markhelp-test-repo/v1.0.0");
    //     $this->assertDirectoryDoesNotExist("{$this->pathDestination}/markhelp-test-repo/v2.0.0");
    //     $this->assertDirectoryDoesNotExist("{$this->pathDestination}/markhelp-test-repo/v3.0.0");
    // }

    // /**
    //  * @test
    //  */
    // public function renderRepositoryCustomConfigs()
    // {
    //     $app = new MarkHelp('https://github.com/ricardopedias/markhelp-test-repo.git');
    //     $app->loadConfigFrom($this->pathExternal . DIRECTORY_SEPARATOR . "config.json");
    //     $app->saveTo($this->pathDestination);

    //     $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/master");
    //     $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/v1.0.0");
    //     $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/v2.0.0");
    //     $this->assertDirectoryExists("{$this->pathDestination}/markhelp-test-repo/v3.0.0");
    // }
}