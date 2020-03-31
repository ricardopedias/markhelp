<?php
declare(strict_types=1);

namespace Tests\App;

use MarkHelp\Bags\Config;
use MarkHelp\App\Reader;
use MarkHelp\App\Writer;
use Tests\TestCase;

class WriterTest extends TestCase
{
    /**
     * @test
     */
    public function render()
    {
        $config = new Config($this->pathRootComplete);
        //$config->setParam('support.document', implode(DIRECTORY_SEPARATOR, [ $this->pathDefaultTheme, 'support', 'document.html']));

        $reader = new Reader($config);

        $writer = new Writer($reader);
        $writer->saveTo($this->pathDestination);

        $this->assertDirectoryExists("{$this->pathDestination}/assets");
        $this->assertDirectoryExists("{$this->pathDestination}/avançado");
        $this->assertDirectoryExists("{$this->pathDestination}/o_básico");
        $this->assertFileExists("{$this->pathDestination}/assets/styles.css");
        $this->assertFileExists("{$this->pathDestination}/home.html");
        $this->assertFileExists("{$this->pathDestination}/page-one.html");
        $this->assertFileExists("{$this->pathDestination}/page-two.html");
    }
}