<?php
declare(strict_types=1);

namespace Tests;

use MarkHelp\App\Tools;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
{
    use Tools;

    protected $pathRootMinimal  = null;
    protected $pathRootDocument = null;
    protected $pathRootMenu     = null;
    protected $pathRootComplete = null;
    protected $pathExternal     = null;
    protected $pathDestination  = null;
    protected $pathDefaultTheme = null;

    protected function setUp() : void
    {
        $this->pathRootMinimal  = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'skeleton-minimal']);
        $this->pathRootDocument = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'skeleton-document']);
        $this->pathRootMenu     = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'skeleton-menu']);
        $this->pathRootComplete = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'skeleton-complete']);
        $this->pathExternal     = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'external']);
        $this->pathDestination  = implode(DIRECTORY_SEPARATOR, [__DIR__, 'test-files', 'destination']);
        $this->pathDefaultTheme = implode(DIRECTORY_SEPARATOR, [ $this->dirname(__DIR__), 'src', 'Themes', 'default']);
    }
}