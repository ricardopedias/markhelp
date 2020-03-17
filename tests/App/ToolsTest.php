<?php
declare(strict_types=1);

namespace Tests\App;

use Exception;
use MarkHelp\Bags\Config;
use MarkHelp\App\Reader;
use MarkHelp\Bags\Asset;
use MarkHelp\Bags\Support;
use Tests\TestCase;

class ToolsTest extends TestCase
{
    /**
     * @test
     */
    public function basenameTest()
    {
        $this->assertEquals('meu-arquivo.md', (new Tools)->basename('/caminho/para/meu-arquivo.md'));
        $this->assertEquals('meu-arquivo', (new Tools)->basename('/caminho/para/meu-arquivo'));
    }

    /**
     * @test
     */
    public function filenameTest()
    {
        $this->assertEquals('meu-arquivo', (new Tools)->filename('/caminho/para/meu-arquivo.md'));
        $this->assertEquals('meu-arquivo.m', (new Tools)->filename('/caminho/para/meu-arquivo.m'));
        $this->assertEquals('meu-arquivo.br', (new Tools)->filename('/caminho/para/meu-arquivo.br.md'));
        $this->assertEquals('meu-arquivo.html', (new Tools)->filename('/caminho/para/meu-arquivo.html.html'));
    }

    /**
     * @test
     */
    public function dirnameTest()
    {
        $this->assertEquals('/caminho/para', (new Tools)->dirname('/caminho/para/meu-arquivo.md'));
        $this->assertEquals('/caminho/para', (new Tools)->dirname('/caminho/para/meu-arquivo'));
    }

    /**
     * @test
     */
    public function absolutePathTest()
    {
        $fullPath = $this->pathExternal;
        $startPath = (new Tools)->dirname($this->pathExternal);
        $this->assertEquals($startPath, (new Tools)->absolutePath($fullPath . "/../"));

        $currentDir = getcwd(); // diretorio onde o composer test-file foi executado
        $this->assertEquals($currentDir, (new Tools)->absolutePath("./"));

        $fakeDir = "$currentDir/teste";
        $this->assertEquals($fakeDir, (new Tools)->absolutePath("/teste"));
        $this->assertEquals($fakeDir, (new Tools)->absolutePath("./teste"));

        $parentDir = (new Tools)->dirname($currentDir);
        $this->assertEquals($parentDir, (new Tools)->absolutePath("./../"));

        $parentFakeDir = "$parentDir/teste";
        $this->assertEquals($parentFakeDir, (new Tools)->absolutePath("./../teste"));
    }
}