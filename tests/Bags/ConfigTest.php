<?php
declare(strict_types=1);

namespace Tests\Bags;

use Exception;
use MarkHelp\Bags\Config;
use ReflectionClass;
use Tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function rootInvalid()
    {
        $this->expectException(Exception::class);
        
        new Config('/not-exists');
    }

    /**
     * @test
     */
    public function setRootNotPermited()
    {
        $this->expectException(Exception::class);

        $bag = new Config($this->pathRootMinimal);
        $bag->setParam("path.root", $this->pathRootMinimal);
    }

    /**
     * @test
     */
    public function setInvalid()
    {
        $this->expectException(Exception::class);

        $bag = new Config($this->pathRootMinimal);
        $bag->requireValue("param.invalid", "");
    }

    /**
     * @test
     */
    public function setRequiredEmpty()
    {
        $this->expectException(Exception::class);

        $bag = new Config($this->pathRootMinimal);
        $bag->requireValue("path.root", "");
    }

    /**
     * @test
     */
    public function setRequiredNull()
    {
        $this->expectException(Exception::class);

        $bag = new Config($this->pathRootMinimal);
        $bag->requireValue("path.root", null);
    }

    /**
     * @test
     */
    public function requireValueEmpty()
    {
        $this->expectException(Exception::class);

        $bag = new Config($this->pathRootMinimal);
        $bag->requireValue("param.teste", "");
    }

    /**
     * @test
     */
    public function requireValueNull()
    {
        $this->expectException(Exception::class);

        $bag = new Config($this->pathRootMinimal);
        $bag->requireValue("param.teste", null);
    }

    /**
     * @test
     */
    public function normalizePath()
    {
        $bag = new Config($this->pathRootMinimal);
        $pathTheme = $bag->param('path.theme');

        $path = $bag->normalizePath('/teste', false);
        $this->assertSame('/teste', $path);

        $path = $bag->normalizePath('/teste/', false);
        $this->assertSame('/teste', $path);

        $path = $bag->normalizePath('{{theme}}/teste/', false);
        $this->assertSame("{$pathTheme}/teste", $path);

        $path = $bag->normalizePath('{{ theme }}/teste/', false);
        $this->assertSame("{$pathTheme}/teste", $path);
    }

    /**
     * @test
     */
    public function normalizePathCheckDirectory()
    {
        $bag = new Config($this->pathRootMinimal);
        $pathTheme = $bag->param('path.theme');

        $path = $bag->normalizePath('/teste', true);
        $this->assertNull($path);

        $path = $bag->normalizePath($pathTheme, true);
        $this->assertSame($pathTheme, $path);

        $path = $bag->normalizePath('{{theme}}/assets/', false);
        $this->assertSame("{$pathTheme}/assets", $path);

        $path = $bag->normalizePath('{{ theme }}/assets/', false);
        $this->assertSame("{$pathTheme}/assets", $path);
    }

    /**
     * @test
     */
    public function normalizeFile()
    {
        $bag = new Config($this->pathRootMinimal);

        $path = $bag->normalizeFile('/not-exists');
        $this->assertNull($path);

        $path = $bag->normalizeFile("{$this->pathExternal}/config.json");
        $this->assertSame("{$this->pathExternal}/config.json", $path);
    }

    /**
     * @test
     */
    public function normalizeValue()
    {
        $bag = new Config($this->pathRootMinimal);
        $defaultProjectName = $bag->param('project.name');
        $this->assertSame('Mark Help', $defaultProjectName);

        $bag->setParam('project.name', 'Teste de Unidade');
        $this->assertSame('Teste de Unidade', $bag->param('project.name'));

        $path = $bag->normalizeValue('project.name', '');
        $this->assertSame($defaultProjectName, $path);

        $path = $bag->normalizeValue('project.name', null);
        $this->assertSame($defaultProjectName, $path);
    }
    
    /**
     * @test
     */
    public function defaultPathRoot()
    {
        $bag = new Config($this->pathRootMinimal);
        $this->assertEquals($this->pathRootMinimal, $bag->param('path.root'));
    }

    /**
     * @test
     */
    public function defaultTheme()
    {
        $bag = new Config($this->pathRootMinimal);

        $reflect = new ReflectionClass(Config::class);
        $parentDir = $this->dirname($this->dirname($reflect->getFilename()));
        $themePath = "{$parentDir}/Themes/default";

        // tema padrão
        $this->assertEquals($themePath, $bag->param('path.theme')); 

        // tema existente
        $bag->setParam('path.theme', "{$this->pathExternal}/themes/one");
        $this->assertEquals("{$this->pathExternal}/themes/one", $bag->param('path.theme')); 

        // Tema nulo -> volta para o padrão
        $bag->setParam('path.theme', null);
        $this->assertEquals($themePath, $bag->param('path.theme')); 

        // Tema vazio -> volta para o padrão
        $bag->setParam('path.theme', "");
        $this->assertEquals($themePath, $bag->param('path.theme')); 

        // Tema inválido -> volta para o padrão
        $bag->setParam('path.theme', true);
        $this->assertEquals($themePath, $bag->param('path.theme')); 

    }

    /**
     * @test
     */
    public function defaultStrings()
    {
        $bag = new Config($this->pathRootMinimal);

        $this->assertSame('Mark Help', $bag->param('project.name'));

        $bag->setParam('project.name', "");
        $this->assertSame('Mark Help', $bag->param('project.name'));

        $bag->setParam('project.name', null);
        $this->assertSame('Mark Help', $bag->param('project.name'));

        $bag->setParam('project.name', true);
        $this->assertSame('1', $bag->param('project.name'));

        $bag->setParam('project.name', 1);
        $this->assertSame('1', $bag->param('project.name'));
    }

    /**
     * @test
     */
    public function defaultBooleans()
    {
        $bag = new Config($this->pathRootMinimal);

        $this->assertSame(true, $bag->param('logo.status'));

        $bag->setParam('logo.status', ""); // convertido para booleano pelo PHP
        $this->assertSame(false, $bag->param('logo.status'));

        $bag->setParam('logo.status', null); // convertido para booleano pelo PHP
        $this->assertSame(false, $bag->param('logo.status'));

        $bag->setParam('logo.status', "any"); // convertido para booleano pelo PHP
        $this->assertSame(true, $bag->param('logo.status'));

        $bag->setParam('logo.status', true);
        $this->assertSame(true, $bag->param('logo.status'));

        $bag->setParam('logo.status', false);
        $this->assertSame(false, $bag->param('logo.status'));

        $bag->setParam('logo.status', 1); // convertido para booleano pelo PHP
        $this->assertSame(true, $bag->param('logo.status'));

        $bag->setParam('logo.status', 0); // convertido para booleano pelo PHP
        $this->assertSame(false, $bag->param('logo.status'));
    }

    /**
     * @test
     */
    public function defaultAssets()
    {
        $bag = new Config($this->pathRootMinimal);

        // tema inicial
        $reflect     = new ReflectionClass(Config::class);
        $parentDir   = $this->dirname($this->dirname($reflect->getFilename()));
        $themePath   = "{$parentDir}/Themes/default";

        $assets = [
            'assets.styles' => "{$themePath}/assets/styles.css",
            'assets.scripts' => "{$themePath}/assets/scripts.js",
            'assets.logo.src' => "{$themePath}/assets/logo.png",
            'assets.icon.favicon' => "{$themePath}/assets/favicon.ico",
            'assets.icon.apple' => "{$themePath}/assets/apple-touch-icon-precomposed.png",
        ];
        
        foreach($assets as $paramName => $defaultFile) {

            // arquivo padrão
            $this->assertEquals($defaultFile, $bag->param($paramName));
    
            // arquivo existente
            $bag->setParam($paramName, "{$this->pathExternal}/themes/one/assets/logo.png");
            $this->assertEquals("{$this->pathExternal}/themes/one/assets/logo.png", $bag->param($paramName)); 
     
            // arquivo nulo -> volta para o padrão
            $bag->setParam($paramName, null);
            $this->assertEquals($defaultFile, $bag->param($paramName)); 
     
            // Tema vazio -> volta para o padrão
            $bag->setParam($paramName, "");
            $this->assertEquals($defaultFile, $bag->param($paramName)); 
     
            // Tema inválido -> volta para o padrão
            $bag->setParam($paramName, true);
            $this->assertEquals($defaultFile, $bag->param($paramName)); 
        }
    }

    /**
     * @test
     */
    public function defaultDocumentFromTheme()
    {
        $bag = new Config($this->pathRootMinimal);
    
        // tema inicial
        $reflect     = new ReflectionClass(Config::class);
        $parentDir   = $this->dirname($this->dirname($reflect->getFilename()));
        $themePath   = "{$parentDir}/Themes/default";
        $defaultFile = "{$themePath}/support/document.html";
        
        // arquivo padrão
        $this->assertEquals($defaultFile, $bag->param('support.document'));

        // arquivo existente
        $bag->setParam('support.document', "{$this->pathExternal}/support/document-custom.html");
        $this->assertEquals("{$this->pathExternal}/support/document-custom.html", $bag->param('support.document')); 
 
        // arquivo nulo -> volta para o padrão
        $bag->setParam('support.document', null);
        $this->assertEquals($defaultFile, $bag->param('support.document')); 
 
        // Tema vazio -> volta para o padrão
        $bag->setParam('support.document', "");
        $this->assertEquals($defaultFile, $bag->param('support.document')); 
 
        // Tema inválido -> volta para o padrão
        $bag->setParam('support.document', true);
        $this->assertEquals($defaultFile, $bag->param('support.document')); 
    }

    /**
     * @test
     */
    public function defaultDocumentFromProject()
    {
        $defaultFile = "{$this->pathRootComplete}/document.html";

        $bag = new Config($this->pathRootComplete);
        $this->assertEquals($defaultFile, $bag->param('support.document'));
    }

    /**
     * @test
     */
    public function customDocument()
    {
        $bag = new Config($this->pathRootComplete);

        $defaultFile = "{$this->pathExternal}/support/document-custom.html";
        $bag->setParam('support.document', $defaultFile);
        
        // arquivo personalizado
        $this->assertEquals($defaultFile, $bag->param('support.document'));
    }

    /**
     * @test
     */
    public function defaultMenuIsNull()
    {
        $bag = new Config($this->pathRootMinimal);
        $this->assertNull($bag->param('support.menu'));
    }

    /**
     * @test
     */
    public function defaultMenuFromProject()
    {
        $defaultFile = "{$this->pathRootComplete}/menu.json";

        $bag = new Config($this->pathRootComplete);
        $this->assertEquals($defaultFile, $bag->param('support.menu'));
    }

    /**
     * @test
     */
    public function customMenu()
    {
        $defaultFile = "{$this->pathExternal}/support/menu-custom.json";

        $bag = new Config($this->pathRootComplete);
        $bag->setParam('support.menu', $defaultFile);
        
        $this->assertEquals($defaultFile, $bag->param('support.menu'));
    }

    /**
     * @test
     */
    public function customMenuInvalidIsNull()
    {
        $defaultFile = "{$this->pathExternal}/support/menu-custom.json";

        $bag = new Config($this->pathRootMinimal);
        $bag->setParam('support.menu', $defaultFile);
        
        // arquivo personalizado
        $this->assertEquals($defaultFile, $bag->param('support.menu'));

        // arquivo nulo -> volta para o padrão
        $bag->setParam('support.menu', null);
        $this->assertNull($bag->param('support.menu')); 
 
        // // Tema vazio -> volta para o padrão
        $bag->setParam('support.menu', "");
        $this->assertNull($bag->param('support.menu')); 
 
        // // Tema inválido -> volta para o padrão
        $bag->setParam('support.menu', true);
        $this->assertNull($bag->param('support.menu')); 
    }

    
}