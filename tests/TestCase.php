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
        $this->pathRoot           = dirname(__DIR__);
        $this->pathSource         = $this->normalizePath("{$this->pathRoot}/src");
        $this->pathDefaultTheme   = $this->normalizePath("{$this->pathSource}/Themes/default");
        $this->pathTests          = __DIR__;
        $this->pathTestFiles      = $this->normalizePath("{$this->pathTests}/test-files");
        $this->pathConfigurations = $this->normalizePath("{$this->pathTestFiles}/configurations");
        $this->pathReleases       = $this->normalizePath("{$this->pathTestFiles}/skeleton-releases");
        $this->pathThemes         = $this->normalizePath("{$this->pathTestFiles}/skeleton-themes");
        $this->pathDestination    = $this->normalizePath("{$this->pathTestFiles}/destination");
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
}