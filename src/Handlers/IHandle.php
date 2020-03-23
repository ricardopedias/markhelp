<?php
declare(strict_types=1);

namespace MarkHelp\Handlers;

use MarkHelp\Bags\Config;

interface IHandle
{
    public function setConfig(Config $instance);

    public function toDestination(string $pathDestination) : void;
}