<?php
declare(strict_types=1);

namespace MarkHelp\Handlers;

use Error;
use Exception;
use MarkHelp\App\Filesystem;
use MarkHelp\App\GitCatcher;
use MarkHelp\App\Reader;
use MarkHelp\App\Tools;
use MarkHelp\App\Writer;
use MarkHelp\Bags\Config;

class RepositoryHandle implements IHandle
{
    use Tools;

    private $config = null;

    private $repository = [];

    public function __construct(string $repository)
    {
        $this->repository = $repository;
    }

    public function setConfig(Config $instance)
    {
        $this->config = $instance;
        return $this;
    }

    public function toDestination(string $pathDestination) : void
    {
        $cloneDir = $this->config->param('clone.directory');
        $cloneBranchs = array_map(function($v){ return trim($v); }, explode(",", $this->config->param('clone.branchs')));

        $gotcha = new GitCatcher;
        $gotcha->addRepo($this->repository, $cloneDir, $cloneBranchs);
        $gotcha->grabTo($pathDestination);

        $allBranchs = current($gotcha->allCloneds());
        foreach($allBranchs as $branchName => $info) {

            $currentConfig = $this->config->all();
            unset($currentConfig['path.root']);

            $newConfig = new Config($pathDestination . DIRECTORY_SEPARATOR . $info['path']);
            $newConfig->addParams($currentConfig);

            $reader = new Reader($newConfig);
            $writer = new Writer($reader);
            $writer->saveTo($pathDestination . DIRECTORY_SEPARATOR . 'rendered' . DIRECTORY_SEPARATOR . $branchName);  
        }
    }
}