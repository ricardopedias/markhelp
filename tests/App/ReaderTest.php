<?php
declare(strict_types=1);

namespace Tests\App;

use Exception;
use MarkHelp\Bags\Config;
use MarkHelp\App\Reader;
use MarkHelp\Bags\Asset;
use MarkHelp\Bags\Support;
use Tests\TestCase;

class ReaderTest extends TestCase
{
    /**
     * @test
     */
    public function loadSupportDocumentDefaultFromTheme()
    {
        $config = new Config($this->pathRootMinimal);
        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(1, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['document']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['document']->param('mountPoint'));
        $this->assertSame('document.html', $reader->supportFiles()['document']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadSupportDocumentSetFromTheme()
    {
        $config = new Config($this->pathRootMinimal);
        $config->setParam('support.document', '{{ theme }}/support/document.html');
        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(1, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['document']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['document']->param('mountPoint'));
        $this->assertSame('document.html', $reader->supportFiles()['document']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadSupportDocumentSetCustom()
    {
        $config = new Config($this->pathRootMinimal);
        $config->setParam('support.document', "{$this->pathExternal}/support/document-custom.html");
        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(1, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['document']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['document']->param('mountPoint'));
        $this->assertSame('document-custom.html', $reader->supportFiles()['document']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadSupportDocumentSetInvalidFallback()
    {
        $config = new Config($this->pathRootMinimal);
        $config->setParam('support.document', null);
        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(1, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['document']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['document']->param('mountPoint'));
        $this->assertSame('document.html', $reader->supportFiles()['document']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadSupportDocumentFromProject()
    {
        $config = new Config($this->pathRootDocument);
        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(1, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['document']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['document']->param('mountPoint'));
        $this->assertEquals('document.html', $reader->supportFiles()['document']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadSupportDocumentCustom()
    {
        $config = new Config($this->pathRootDocument);
        $config->setParam('support.document', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'support', 'document-custom.html']));

        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(1, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['document']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['document']->param('mountPoint'));
        $this->assertEquals('document-custom.html', $reader->supportFiles()['document']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadSupportMenuOrigin()
    {
        $config = new Config($this->pathRootMenu);
        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(2, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['menu']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['menu']->param('mountPoint'));
        $this->assertEquals('menu.json', $reader->supportFiles()['menu']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadSupportMenuCustom()
    {
        $config = new Config($this->pathRootMenu);
        $config->setParam('support.menu', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'support', 'menu-custom.json']));

        $reader = new Reader($config);

        $this->assertIsArray($reader->supportFiles());
        $this->assertCount(2, $reader->supportFiles());
        $this->assertInstanceOf(Support::class, $reader->supportFiles()['menu']);
        $this->assertStringContainsString('mount_', $reader->supportFiles()['menu']->param('mountPoint'));
        $this->assertEquals('menu-custom.json', $reader->supportFiles()['menu']->param('supportPath'));
    }

    /**
     * @test
     */
    public function loadAssetsTheme()
    {
        $config = new Config($this->pathRootMinimal);
        // os assets estão sendo carregados do tema padrão
        $reader = new Reader($config);

        $this->assertIsArray($reader->assetsFiles());
        $this->assertCount(5, $reader->assetsFiles());

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.styles']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.styles']->param('mountPoint'));
        $this->assertEquals('styles.css', $reader->assetsFiles()['assets.styles']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.scripts']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.scripts']->param('mountPoint'));
        $this->assertEquals('scripts.js', $reader->assetsFiles()['assets.scripts']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.logo.src']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.logo.src']->param('mountPoint'));
        $this->assertEquals('logo.png', $reader->assetsFiles()['assets.logo.src']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.icon.favicon']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.icon.favicon']->param('mountPoint'));
        $this->assertEquals('favicon.ico', $reader->assetsFiles()['assets.icon.favicon']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.icon.apple']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.icon.apple']->param('mountPoint'));
        $this->assertEquals('apple-touch-icon-precomposed.png', $reader->assetsFiles()['assets.icon.apple']->param('assetPath'));
    }

    /**
     * @test
     */
    public function loadAssetsCustomTheme()
    {
        $config = new Config($this->pathRootMinimal);
        $config->setParam('path.theme', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'themes', 'one']));
        $reader = new Reader($config);

        $this->assertIsArray($reader->assetsFiles());
        $this->assertCount(5, $reader->assetsFiles());

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.styles']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.styles']->param('mountPoint'));
        $this->assertEquals('styles.css', $reader->assetsFiles()['assets.styles']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.scripts']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.scripts']->param('mountPoint'));
        $this->assertEquals('scripts.js', $reader->assetsFiles()['assets.scripts']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.logo.src']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.logo.src']->param('mountPoint'));
        $this->assertEquals('logo.png', $reader->assetsFiles()['assets.logo.src']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.icon.favicon']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.icon.favicon']->param('mountPoint'));
        $this->assertEquals('favicon.ico', $reader->assetsFiles()['assets.icon.favicon']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.icon.apple']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.icon.apple']->param('mountPoint'));
        $this->assertEquals('apple-touch-icon-precomposed.png', $reader->assetsFiles()['assets.icon.apple']->param('assetPath'));
    }

    /**
     * @test
     */
    public function loadAssetsCustomFiles()
    {
        $config = new Config($this->pathRootMinimal);
        $config->setParam('assets.styles', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'custom-styles.css']));
        $config->setParam('assets.scripts', implode(DIRECTORY_SEPARATOR, [$this->pathExternal, 'custom-scripts.js']));

        $reader = new Reader($config);

        $this->assertIsArray($reader->assetsFiles());
        $this->assertCount(5, $reader->assetsFiles());

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.styles']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.styles']->param('mountPoint'));
        $this->assertEquals('custom-styles.css', $reader->assetsFiles()['assets.styles']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.scripts']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.scripts']->param('mountPoint'));
        $this->assertEquals('custom-scripts.js', $reader->assetsFiles()['assets.scripts']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.logo.src']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.logo.src']->param('mountPoint'));
        $this->assertEquals('logo.png', $reader->assetsFiles()['assets.logo.src']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.icon.favicon']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.icon.favicon']->param('mountPoint'));
        $this->assertEquals('favicon.ico', $reader->assetsFiles()['assets.icon.favicon']->param('assetPath'));

        $this->assertInstanceOf(Asset::class, $reader->assetsFiles()['assets.icon.apple']);
        $this->assertStringContainsString('mount_', $reader->assetsFiles()['assets.icon.apple']->param('mountPoint'));
        $this->assertEquals('apple-touch-icon-precomposed.png', $reader->assetsFiles()['assets.icon.apple']->param('assetPath'));
    }

    /**
     * @test
     */
    public function loadMarkdownOneLevel()
    {
        $config = new Config($this->pathRootMinimal);
        $reader = new Reader($config);
        $this->assertIsArray($reader->markdownFiles());
        $this->assertCount(3, $reader->markdownFiles());

        $this->assertEquals('./', $reader->markdownFiles()['index']->param('assetsPrefix'));
        $this->assertEquals('index.md', $reader->markdownFiles()['index']->param('pathSearch'));
        $this->assertEquals('home.html', $reader->markdownFiles()['index']->param('pathReplace'));

        $this->assertEquals('./', $reader->markdownFiles()['page-one']->param('assetsPrefix'));
        $this->assertEquals('page-one.md', $reader->markdownFiles()['page-one']->param('pathSearch'));
        $this->assertEquals('page-one.html', $reader->markdownFiles()['page-one']->param('pathReplace'));

        $this->assertEquals('./', $reader->markdownFiles()['page-two']->param('assetsPrefix'));
        $this->assertEquals('page-two.md', $reader->markdownFiles()['page-two']->param('pathSearch'));
        $this->assertEquals('page-two.html', $reader->markdownFiles()['page-two']->param('pathReplace'));
    }

    /**
     * @test
     */
    public function loadMarkdownMultilevel()
    {
        $config = new Config($this->pathRootComplete);
        $reader = new Reader($config);
        $this->assertIsArray($reader->markdownFiles());
        $this->assertCount(7, $reader->markdownFiles());

        // raiz

        $this->assertEquals('./', $reader->markdownFiles()['index']->param('assetsPrefix'));
        $this->assertEquals('index.md', $reader->markdownFiles()['index']->param('pathSearch'));
        $this->assertEquals('home.html', $reader->markdownFiles()['index']->param('pathReplace'));

        $this->assertEquals('./', $reader->markdownFiles()['page-one']->param('assetsPrefix'));
        $this->assertEquals('page-one.md', $reader->markdownFiles()['page-one']->param('pathSearch'));
        $this->assertEquals('page-one.html', $reader->markdownFiles()['page-one']->param('pathReplace'));

        $this->assertEquals('./', $reader->markdownFiles()['page-two']->param('assetsPrefix'));
        $this->assertEquals('page-two.md', $reader->markdownFiles()['page-two']->param('pathSearch'));
        $this->assertEquals('page-two.html', $reader->markdownFiles()['page-two']->param('pathReplace'));

        // segundo nível

        $this->assertEquals('./../', $reader->markdownFiles()['page-three']->param('assetsPrefix'));
        $this->assertEquals('Avançado/page-three.md', $reader->markdownFiles()['page-three']->param('pathSearch'));
        $this->assertEquals('avançado/page-three.html', $reader->markdownFiles()['page-three']->param('pathReplace'));

        $this->assertEquals('./../', $reader->markdownFiles()['page-four']->param('assetsPrefix'));
        $this->assertEquals('Avançado/page-four.md', $reader->markdownFiles()['page-four']->param('pathSearch'));
        $this->assertEquals('avançado/page-four.html', $reader->markdownFiles()['page-four']->param('pathReplace'));

        $this->assertEquals('./../', $reader->markdownFiles()['page-five']->param('assetsPrefix'));
        $this->assertEquals('O Básico/page-five.md', $reader->markdownFiles()['page-five']->param('pathSearch'));
        $this->assertEquals('o_básico/page-five.html', $reader->markdownFiles()['page-five']->param('pathReplace'));

        $this->assertEquals('./../', $reader->markdownFiles()['page-six']->param('assetsPrefix'));
        $this->assertEquals('O Básico/page-six.md', $reader->markdownFiles()['page-six']->param('pathSearch'));
        $this->assertEquals('o_básico/page-six.html', $reader->markdownFiles()['page-six']->param('pathReplace'));
    }
}