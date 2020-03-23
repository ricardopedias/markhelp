<?php
declare(strict_types=1);

namespace MarkHelp\Handlers;

use MarkHelp\App\Reader;
use MarkHelp\App\Tools;
use MarkHelp\App\Writer;
use MarkHelp\Bags\Config;

class LocalHandle implements IHandle
{
    use Tools;

    private $config = null;

    public function setConfig(Config $instance)
    {
        $this->config = $instance;
        return $this;
    }

    public function toDestination(string $pathDestination) : void
    {
        $reader = new Reader($this->config);
        $writer = new Writer($reader);
        $writer->saveTo($pathDestination);
    }
}