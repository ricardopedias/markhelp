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

    private $pathOrigin = null;

    private $configList = [];

    public function setOrigin(string $pathOrigin)
    {
        $this->pathOrigin = $pathOrigin;
        return $this;
    }

    public function setConfigList(array $params)
    {
        $this->configList = $params;
        return $this;
    }

    public function toDestination(string $pathDestination) : void
    {
        $config = new Config($this->pathOrigin);
        $config->addParams($this->configList);

        $reader = new Reader($config);
        $writer = new Writer($reader);
        $writer->saveTo($pathDestination);
    }
}