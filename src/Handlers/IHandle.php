<?php
declare(strict_types=1);

namespace MarkHelp\Handlers;

use MarkHelp\Bags\Config;

interface IHandle
{
    public function setOrigin(string $pathOrigin);

    public function setConfigList(array $instance);

    public function toDestination(string $pathDestination) : void;
}