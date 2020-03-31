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

    private $gitUrl = null;

    private $configList = [];

    public function setOrigin(string $pathOrigin)
    {
        $this->gitUrl = $pathOrigin;
        return $this;
    }
    
    public function setConfigList(array $params)
    {
        $this->configList = $params;
        return $this;
    }

    public function toDestination(string $pathDestination) : void
    {
        $defaultConfig = new Config($pathDestination);
        $defaultConfig->addParams($this->configList);

        $cloneDir = $defaultConfig->param('clone.directory');
        $cloneBranchs = array_map(function($v){ return trim($v); }, explode(",", $defaultConfig->param('clone.branchs')));

        $gotcha = new GitCatcher;
        $gotcha->addRepo($this->gitUrl, $cloneDir, $cloneBranchs);
        $gotcha->grabTo($pathDestination);

        $repoName   = key($gotcha->allCloneds());
        $allBranchs = current($gotcha->allCloneds());
        $versions   = array_map(function($v){ return explode('/', $v['path'])[1]; }, current($gotcha->allCloneds()));
        foreach($allBranchs as $branchName => $item) {

            $config = new Config($pathDestination . DIRECTORY_SEPARATOR . $item['path']);
            $config->addParams($this->configList);

            $reader = new Reader($config);
            $reader->setCurrentVersion($branchName);
            foreach($versions as $versionLabel) {
                $versionUrl = "$versionLabel";
                $reader->addVersion($versionLabel, $versionUrl, true);
            }

            $writer = new Writer($reader);
            $writer->saveTo($pathDestination . DIRECTORY_SEPARATOR . 'rendered' . DIRECTORY_SEPARATOR . $branchName);  
        }

        $filesystem = new Filesystem;
        $filesystem->mount('destination', $pathDestination);

        $filesystem->deleteDir("destination://{$repoName}");
        $filesystem->moveDirectory("destination://rendered", "destination://{$repoName}");
    }
}