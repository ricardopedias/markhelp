<?php
declare(strict_types=1);

namespace Tests\Bags;

use Exception;
use MarkHelp\Bags\Config;
use MarkHelp\Bags\ConfigNormalize;
use ReflectionClass;
use Tests\TestCase;

class ConfigNormalizeTest extends TestCase
{
    /** @test */
    public function normalizePath()
    {
        $pathRoot  = $this->pathRootMinimal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizePath('/teste');
        $this->assertSame('/teste', $path);

        $path = $normalizer->normalizePath('/teste/');
        $this->assertSame('/teste', $path);
    }

    /** @test */
    public function normalizePathReplaces()
    {
        $pathRoot  = $this->pathRootMinimal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizePath('{{theme}}/teste');
        $this->assertSame("{$pathTheme}/teste", $path);

        $path = $normalizer->normalizePath('{{theme}}/teste/');
        $this->assertSame("{$pathTheme}/teste", $path);

        $path = $normalizer->normalizePath('{{ theme }}/teste');
        $this->assertSame("{$pathTheme}/teste", $path);

        $path = $normalizer->normalizePath('{{ theme }}/teste/');
        $this->assertSame("{$pathTheme}/teste", $path);

        $path = $normalizer->normalizePath('{{project}}/teste');
        $this->assertSame("{$pathRoot}/teste", $path);

        $path = $normalizer->normalizePath('{{project}}/teste/');
        $this->assertSame("{$pathRoot}/teste", $path);

        $path = $normalizer->normalizePath('{{ project }}/teste');
        $this->assertSame("{$pathRoot}/teste", $path);

        $path = $normalizer->normalizePath('{{ project }}/teste/');
        $this->assertSame("{$pathRoot}/teste", $path);
    }

    /** @test */
    public function normalizePathCheckDirectory()
    {
        $pathRoot  = $this->pathRootMinimal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizePath('/teste', true);
        $this->assertNull($path);

        $path = $normalizer->normalizePath($pathTheme, true);
        $this->assertSame($pathTheme, $path);
    }

    /** @test */
    public function normalizePathCheckDirectoryReplaces()
    {
        $pathRoot  = $this->pathRootMinimal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizePath('{{theme}}', true);
        $this->assertSame("{$pathTheme}", $path);

        $path = $normalizer->normalizePath('{{theme}}/', true);
        $this->assertSame("{$pathTheme}", $path);

        $path = $normalizer->normalizePath('{{ theme }}', true);
        $this->assertSame("{$pathTheme}", $path);

        $path = $normalizer->normalizePath('{{ theme }}/', true);
        $this->assertSame("{$pathTheme}", $path);

        $path = $normalizer->normalizePath('{{project}}', true);
        $this->assertSame("{$pathRoot}", $path);

        $path = $normalizer->normalizePath('{{project}}/', true);
        $this->assertSame("{$pathRoot}", $path);

        $path = $normalizer->normalizePath('{{ project }}', true);
        $this->assertSame("{$pathRoot}", $path);

        $path = $normalizer->normalizePath('{{ project }}/', true);
        $this->assertSame("{$pathRoot}", $path);
    }

    /** @test */
    public function normalizeFile()
    {
        $pathRoot  = $this->pathExternal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizeFile('/not-exists');
        $this->assertNull($path);

        $path = $normalizer->normalizeFile("{$this->pathExternal}/config.json");
        $this->assertSame("{$this->pathExternal}/config.json", $path);
    }

    /** @test */
    public function normalizeFileReplaces()
    {
        $pathRoot  = $this->pathRootMinimal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizePath('{{theme}}/teste.php');
        $this->assertSame("{$pathTheme}/teste.php", $path);

        $path = $normalizer->normalizePath('{{ theme }}/teste.php');
        $this->assertSame("{$pathTheme}/teste.php", $path);

        $path = $normalizer->normalizePath('{{project}}/teste.php');
        $this->assertSame("{$pathRoot}/teste.php", $path);

        $path = $normalizer->normalizePath('{{ project }}/teste.php');
        $this->assertSame("{$pathRoot}/teste.php", $path);
    }

    /** @test */
    public function normalizeValue()
    {
        $pathRoot  = $this->pathRootMinimal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizeValue('');
        $this->assertSame('', $path);

        $path = $normalizer->normalizeValue('CleanCode');
        $this->assertSame('CleanCode', $path);
    }

    /** @test */
    public function normalizeValueReplaces()
    {
        $pathRoot  = $this->pathRootMinimal;
        $pathTheme = $this->pathDefaultTheme;
        $normalizer = new ConfigNormalize($pathRoot, $pathTheme);

        $path = $normalizer->normalizeValue('{{theme}}/teste.txt');
        $this->assertSame("{$pathTheme}/teste.txt", $path);

        $path = $normalizer->normalizeValue('{{ theme }}/teste.txt');
        $this->assertSame("{$pathTheme}/teste.txt", $path);

        $path = $normalizer->normalizeValue('{{project}}/teste.txt');
        $this->assertSame("{$pathRoot}/teste.txt", $path);

        $path = $normalizer->normalizeValue('{{ project }}/teste.txt');
        $this->assertSame("{$pathRoot}/teste.txt", $path);
    }
}
