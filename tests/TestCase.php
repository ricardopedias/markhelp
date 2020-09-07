<?php
declare(strict_types=1);

namespace Tests;

use Exception;
use MarkHelp\App\Tools;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
{
    protected $pathRootMinimal  = null;
    protected $pathRootDocument = null;
    protected $pathRootMenu     = null;
    protected $pathRootComplete = null;
    protected $pathBranchs      = null;
    protected $pathExternal     = null;
    protected $pathDestination  = null;
    protected $pathDefaultTheme = null;

    protected function setUp() : void
    {
        $this->pathSource       = $this->normalizePath(dirname(__DIR__) . '/src');
        $this->pathDefaultTheme = $this->normalizePath("{$this->pathSource}/Themes/default");
        $this->pathTests        = __DIR__;
        $this->pathTestFiles    = $this->normalizePath("{$this->pathTests}/test-files");
        $this->pathReleases     = $this->normalizePath("{$this->pathTestFiles}/skeleton-releases");
        $this->pathThemes       = $this->normalizePath("{$this->pathTestFiles}/skeleton-themes");
        $this->pathDestination  = $this->normalizePath("{$this->pathTestFiles}/destination");


        $this->pathComplete     = $this->normalizePath("{$this->pathTestFiles}/skeleton-complete");

        $this->pathRootMinimal  = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'skeleton-minimal']);
        $this->pathRootDocument = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'skeleton-document']);
        $this->pathRootMenu     = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'skeleton-menu']);
        
        $this->pathExternal     = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'external']);
        $this->pathThemeAltWithDocument = implode(DIRECTORY_SEPARATOR, [ __DIR__, 'test-files', 'theme-with-document']);
        $this->pathThemeAltNotDocument = implode(DIRECTORY_SEPARATOR, [ __DIR__, 'test-files', 'theme-with-document']);
    }

    protected function normalizePath(string $path): string
    {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }

    protected function cleanDestination(): void
    {
        $directory = reliability()->mountDirectory($this->pathDestination);

        $cleanup = $directory->listContents('/');
        foreach ($cleanup as $item) {
            if ($item['type'] === 'dir') {
                $directory->deleteDir("{$item['path']}");
                continue;
            }
            $directory->delete("{$item['path']}");
        }
    }

    // /**
    //  * Obtém o nome + extensão de um arquivo especificado.
    //  * Ex: /dir/meu-arquivo.md -> meu-arquivo.md
    //  */
    // public function basename($filename)
    // {
    //     $filename = filter_var($filename, FILTER_SANITIZE_STRING);
    //     return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    // }

    // /**
    //  * Obtém o nome de um arquivo especificado.
    //  * Ex: /dir/meu-arquivo.md -> meu-arquivo
    //  */
    // public function filename($filename)
    // {
    //     $basename = $this->basename($filename);
    //     return preg_replace('/\\.[^.\\s]{2,}$/', '', $basename);
    // }

    // /**
    //  * Obtém o nome de um diretório com base no caminho especificado.
    //  * Ex: /dir/meu-arquivo.md -> /dir
    //  */
    // public function dirname($filename)
    // {
    //     $basename = $this->basename($filename);
    //     return rtrim(str_replace("/" . $basename, '', $filename), '/');
    // }

    // /**
    //  * Obtém o caminho absoluto do caminho relativo informado.
    //  * @see https://www.php.net/manual/en/function.realpath.php
    //  */
    // public function absolutePath(string $path): ?string
    // {
    //     if (DIRECTORY_SEPARATOR !== '/') {
    //         $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
    //     }
    //     $search = explode('/', $path);
    //     $search = array_filter($search, function ($part) {
    //         return $part !== '.';
    //     });

    //     $append = [];
    //     $match  = false;
    //     while (count($search) > 0) {
    //         $match = realpath(implode('/', $search));
    //         if ($match !== false) {
    //             break;
    //         }
    //         array_unshift($append, array_pop($search));
    //     }
    //     if ($match === false) {
    //         $match = getcwd();
    //     }
    //     if (count($append) > 0) {
    //         $match .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $append);
    //     }
    //     return $match === false ? null : $match;
    // }

    // public function isFile($filename)
    // {
    //     $filename = filter_var($filename, FILTER_SANITIZE_STRING);
    //     return is_file($filename);
    // }

    // public function isDirectory($path)
    // {
    //     $path = filter_var($path, FILTER_SANITIZE_STRING);
    //     return is_dir($path);
    // }

    // public function isDirectoryOrException(string $path): bool
    // {
    //     if ($path === "" || $this->isDirectory($path) === false) {
    //         throw new Exception("The path {$path} does not exist or is not valid");
    //     }

    //     return true;
    // }
}