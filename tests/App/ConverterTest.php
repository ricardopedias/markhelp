<?php
declare(strict_types=1);

namespace Tests\App;

use Exception;
use MarkHelp\App\Converter;
use MarkHelp\App\Filesystem;
use MarkHelp\Bags\Support;
use Tests\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @test
     */
    public function renderString()
    {
        $filesystem = new Filesystem;

        $converter = new Converter($filesystem);
        $html = $converter->render("# Teste {{ teste.string.markdown }}", [
            'teste.string.markdown' => 'Mark Help'
        ]);

        $this->assertStringContainsString("<h1>Teste Mark Help</h1>", $html);

        // Quando um documento não é especificado, uma estrutura padrão é usada
        // <main>
        //    <menu>{{ sidemenu }}</menu>
        //    <article>{{ content }}</article>
        // </main>

        // Não há menhum menu a renderizar
        $this->assertStringContainsString("<menu></menu>", $html);

        // Não há menhum documento a renderizar, mas estrutura padrão foi usada
        $this->assertStringContainsString("<article><h1>Teste Mark Help</h1>", $html);
    }

    /**
     * @test
     */
    public function renderWithDocument()
    {
        $filesystem = new Filesystem;
        $filesystem->mount('custom', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'support']));

        $documentBag = new Support;
        $documentBag->setParam('mountPoint', 'custom');
        $documentBag->setParam('supportPath', 'document-custom.html');

        $converter = new Converter($filesystem);
        $converter->useDocument($documentBag);
        $html = $converter->render("# Teste {{ teste.string.markdown }}", [
            'teste.string.markdown' => 'Mark Help'
        ]);

        $this->assertStringContainsString("<h1>Teste Mark Help</h1>", $html);

        // Não há menhum menu a renderizar
        $this->assertStringContainsString("<menu></menu>", $html);

        // conteúdo do arquivo document-custom.html
        $this->assertStringContainsString("<h1>Teste Documento</h1>", $html);
        $this->assertStringContainsString("<span>Documento Personalizado</span>", $html);
        $this->assertStringContainsString("<article><h1>Teste Mark Help</h1>", $html);
    }

    /**
     * @test
     */
    public function renderWithDocumentMountException()
    {
        $this->expectException(Exception::class);

        $filesystem = new Filesystem;

        $documentBag = new Support;
        $documentBag->setParam('mountPoint', 'custom'); // este ponto de montagem não foi executado no Filesystem
        $documentBag->setParam('supportPath', 'document-custom.html');

        $converter = new Converter($filesystem);
        $converter->useDocument($documentBag);
    }

    /**
     * @test
     */
    public function renderWithMenu()
    {
        $filesystem = new Filesystem;
        $filesystem->mount('custom', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'support']));

        $menuBag = new Support;
        $menuBag->setParam('mountPoint', 'custom');
        $menuBag->setParam('supportPath', 'menu-custom.json');
    
        $converter = new Converter($filesystem);
        $converter->useMenu($menuBag);
        $html = $converter->render("# Teste {{ teste.string.markdown }}", [
            'teste.string.markdown' => 'Mark Help',
            'teste.menu.item' => 'Menu'
        ]);

        // 1º item de menu no arquivo menu-custom.json
        $this->assertStringContainsString("Teste Menu", $html);
        
        // Não há menhum documento a renderizar, mas estrutura padrão foi usada
        $this->assertStringContainsString("<article><h1>Teste Mark Help</h1>", $html);
    }

    /**
     * @test
     */
    public function renderWithMenuMountException()
    {
        $this->expectException(Exception::class);

        $filesystem = new Filesystem;

        $menuBag = new Support;
        $menuBag->setParam('mountPoint', 'custom'); // este ponto de montagem não foi executado no Filesystem
        $menuBag->setParam('supportPath', 'menu-custom.json');

        $converter = new Converter($filesystem);
        $converter->useDocument($menuBag);
    }

    /**
     * @test
     */
    public function renderWithMenuFormatException()
    {
        $this->expectException(Exception::class);
        
        $filesystem = new Filesystem;
        $filesystem->mount('custom', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'support']));

        $menuBag = new Support;
        $menuBag->setParam('mountPoint', 'custom');
        $menuBag->setParam('supportPath', 'menu-custom-invalid.json');
    
        $converter = new Converter($filesystem);
        $converter->useMenu($menuBag);
        $converter->render("# Teste {{ teste.string.markdown }}");
    }

    /**
     * @test
     */
    public function renderWithAll()
    {
        $filesystem = new Filesystem;
        $filesystem->mount('custom', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'support']));

        $documentBag = new Support;
        $documentBag->setParam('mountPoint', 'custom');
        $documentBag->setParam('supportPath', 'document-custom.html');

        $menuBag = new Support;
        $menuBag->setParam('mountPoint', 'custom');
        $menuBag->setParam('supportPath', 'menu-custom.json');
    
       $converter = new Converter($filesystem);
        $converter->useDocument($documentBag);
        $converter->useMenu($menuBag);
        $html = $converter->render("# Teste {{ teste.string.markdown }}", [
            'teste.string.markdown' => 'Mark Help',
            'teste.menu.item' => 'Menu'
        ]);

        // 1º item de menu no arquivo menu-custom.json
        $this->assertStringContainsString("Teste Menu", $html);

        // conteúdo do arquivo document-custom.html
        $this->assertStringContainsString("<h1>Teste Documento</h1>", $html);
        $this->assertStringContainsString("<span>Documento Personalizado</span>", $html);
        $this->assertStringContainsString("<article><h1>Teste Mark Help</h1>", $html);
    }
}