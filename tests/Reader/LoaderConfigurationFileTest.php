<?php

declare(strict_types=1);

namespace Tests\Reader;

use Exception;
use MarkHelp\Reader\Loader;
use Tests\TestCase;

class LoaderConfigurationFileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function projectSilgleReleaseAutoloadConfig()
    {
        $loader = new Loader();
        
        $projectPath = $this->normalizePath("{$this->pathReleases}/v4.0.0");
        $loader->fromLocalDirectory($projectPath);

        $this->assertFalse($loader->hasReleases());

        $this->assertEquals($this->pathDefaultTheme, $loader->config("path_theme"));
        $this->assertEquals('http://www.ricardopedias.com.br', $loader->config("clone_url"));
        $this->assertEquals('/docs', $loader->config("clone_directory"));
        $this->assertEquals('v0.0.05, v1.66.5555', $loader->config("clone_tags"));
        $this->assertEquals('Ricardo Pereira', $loader->config("copy_name"));
        $this->assertEquals('https://github.com/ricardopedias', $loader->config("copy_url"));
        $this->assertEquals("Documentação Fácil", $loader->config("project_name"));
        $this->assertEquals("Fazedor de documentos com buniteza", $loader->config("project_slogan"));
        $this->assertEquals("true", $loader->config("project_fork"));
        $this->assertEquals("Este é um projeto bem legal", $loader->config("project_description"));
        $this->assertEquals("enabled", $loader->config("project_logo_status"));
        $this->assertEquals("images/logo.png", $loader->config("project_logo"));
    }

    /** @test */
    public function projectMultiReleasesAutoloadConfig()
    {
        $loader = new Loader();
         
        $projectPath = $this->normalizePath($this->pathReleases);
        $loader->fromLocalDirectory($projectPath);

        $this->assertTrue($loader->hasReleases());

        $this->assertEquals($this->pathDefaultTheme, $loader->config("path_theme"));
        $this->assertEquals('http://www.ricardopedias.com.br', $loader->config("clone_url"));
        $this->assertEquals('/docs', $loader->config("clone_directory"));
        $this->assertEquals('v0.0.05, v1.66.5555', $loader->config("clone_tags"));
        $this->assertEquals('Ricardo Pereira', $loader->config("copy_name"));
        $this->assertEquals('https://github.com/ricardopedias', $loader->config("copy_url"));
        $this->assertEquals("Documentação Fácil", $loader->config("project_name"));
        $this->assertEquals("Fazedor de documentos com buniteza", $loader->config("project_slogan"));
        $this->assertEquals("true", $loader->config("project_fork"));
        $this->assertEquals("Este é um projeto bem legal", $loader->config("project_description"));
        $this->assertEquals("enabled", $loader->config("project_logo_status"));
        $this->assertEquals("images/logo.png", $loader->config("project_logo"));
    }

    /** @test */
    public function loadConfigFrom()
    {
        $configFile = $this->normalizePath("{$this->pathConfigurations}/config.json");

        $loader = new Loader();
        $loader->loadConfigFrom($configFile);

        $this->assertEquals($this->pathDefaultTheme, $loader->config("path_theme"));
        $this->assertEquals('http://www.ricardopedias.com.br', $loader->config("clone_url"));
        $this->assertEquals('/docs', $loader->config("clone_directory"));
        $this->assertEquals('v0.0.05, v1.66.5555', $loader->config("clone_tags"));
        $this->assertEquals('Ricardo Pereira', $loader->config("copy_name"));
        $this->assertEquals('https://github.com/ricardopedias', $loader->config("copy_url"));

        $this->assertEquals("Documentação Fácil", $loader->config("project_name"));
        $this->assertEquals("Fazedor de documentos com buniteza", $loader->config("project_slogan"));
        $this->assertEquals("true", $loader->config("project_fork"));
        $this->assertEquals("Este é um projeto bem legal", $loader->config("project_description"));
        $this->assertEquals("enabled", $loader->config("project_logo_status"));
        $this->assertEquals("/logo.png", $loader->config("project_logo"));
    }

    /** @test */
    public function loadConfigFromInvalidFormat()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Parameter generate_phpindex does not contain a valid value");

        $configFile = $this->normalizePath("{$this->pathConfigurations}/config-invalid.json");
        $loader = new Loader();
        $loader->loadConfigFrom($configFile);
    }

    /** @test */
    public function loadConfigFromInvalidSintax()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches("/The config file does not contain a json.*/");
        
        $configFile = $this->normalizePath("{$this->pathConfigurations}/config-sintax.json");
        $loader = new Loader();
        $loader->loadConfigFrom($configFile);
    }
}