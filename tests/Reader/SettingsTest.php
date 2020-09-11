<?php

declare(strict_types=1);

namespace Tests\Reader;

use Exception;
use MarkHelp\Reader\Settings;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    /** @test */
    public function setRoot()
    {
        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");

        $config = new Settings();
        $config->setParam("path_project", $projectPath);
        $this->assertEquals($projectPath, $config->param('path_project'));

        // Barra no final deve ser removida de caminhos
        $config = new Settings();
        $config->setParam("path_project", $projectPath . DIRECTORY_SEPARATOR);
        $this->assertNotEquals($projectPath . DIRECTORY_SEPARATOR, $config->param('path_project'));
        $this->assertEquals($projectPath, $config->param('path_project'));
    }

    /** @test */
    public function changeRootNotPermited()
    {
        $this->expectException(Exception::class);

        $projectPath = $this->normalizePath("{$this->pathReleases}/v1.0.0");

        $config = new Settings();
        $config->setParam("path_project", $projectPath);
        $config->setParam("path_project", $projectPath); // <- bip
    }

    /** @test */
    public function setInvalid()
    {
        $this->expectException(Exception::class);

        $config = new Settings();
        $config->setParam("param_invalid", ""); // parâmetro não existe
    }

    /** @test */
    public function defaults()
    {
        $config = new Settings();

        $this->assertSame('', $config->param('path_project'));
        $this->assertSame("{$this->pathSource}/Themes/default", $config->param('path_theme'));

        $this->assertSame('https://github.com/ricardopedias/markhelp', $config->param('clone_url'));
        $this->assertSame('docs', $config->param('clone_directory'));
        $this->assertSame('dev-master', $config->param('clone_tags'));

        $this->assertSame('Ricardo Pereira', $config->param('copy_name'));
        $this->assertSame('http://www.ricardopedias.com.br', $config->param('copy_url'));

        $this->assertSame('Mark Help', $config->param('project_name'));
        $this->assertSame('Gerador de documentação', $config->param('project_slogan'));
        $this->assertSame('true', $config->param('project_fork'));
        $this->assertSame('Gerador de documentação feito em PHP', $config->param('project_description'));
        $this->assertSame('/images/logo.png', $config->param('project_logo'));
        $this->assertSame('enabled', $config->param('project_logo_status'));
    }

    /** @test */
    public function setParamStripRightBar()
    {
        $themePath    = $this->normalizePath("{$this->pathThemes}/with-templates");
        $themePathBar = $this->normalizePath("{$this->pathThemes}/with-templates/");

        $config = new Settings();
        $config->setParam('path_theme', $themePathBar);
        // a barra final deve ser removida
        $this->assertSame($themePath, $config->param('path_theme'));
    }

    /** @test */
    public function setParamReplaceTemplateTags()
    {
        // tag {{theme}}
        $config = new Settings();
        $config->setParam('project_logo', '{{theme}}/teste/de/logo.png');
        $themePath = $config->param('path_theme');
        $this->assertSame("{$themePath}/teste/de/logo.png", $config->param('project_logo'));

        // tag {{ theme }} (com espaços)
        $config = new Settings();
        $config->setParam('project_logo', '{{ theme }}/teste/de/diretorio/');
        $themePath = $config->param('path_theme');
        $this->assertSame("{$themePath}/teste/de/diretorio", $config->param('project_logo'));
    }

    /** @test */
    public function setParamThemePath()
    {
        $themePath = $this->normalizePath("{$this->pathThemes}/with-templates");

        // Tema existente é permitido
        $config = new Settings();
        $config->setParam('path_theme', $themePath);
        $themePath = $config->param('path_theme');
        $this->assertSame($themePath, $config->param('path_theme'));

        // Tema inexistente não é permitido
        $config = new Settings();
        $config->setParam('path_theme', '/path/not/exists');
        $this->assertSame("{$this->pathSource}/Themes/default", $config->param('path_theme'));

        // Tema vazio não é permitido
        $config = new Settings();
        $config->setParam('path_theme', '');
        $this->assertSame("{$this->pathSource}/Themes/default", $config->param('path_theme'));
    }
}