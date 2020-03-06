<?php
declare(strict_types=1);

namespace MarkHelp\App;

use League\Flysystem\Adapter;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\MountManager;

class Filesystem extends MountManager
{
    private $filesystem = null;

    /**
     * Constrói um gerenciador de arquivos.
     * O argumento $storages deve ser um array associativo, 
     * contendo o nome do 'storage' e seu respectivo caminho.
     * Ex: 
     * array(
     *     'documentos' => '/home/ricardo/documentos',
     *     'videos' => '/home/ricardo/videos',
     * )
     * 
     * @param array $storages
     * @see https://flysystem.thephpleague.com/v1/docs/advanced/mount-manager/
     */
    public function __construct(array $filesystems = [])
    {
        foreach($filesystems as $name => $realPath) {

            $adapter = new Adapter\Local($realPath);
            $filesystems[$name] = new LeagueFilesystem($adapter);    
        }

        parent::__construct($filesystems);
    }

    /**
     * Monta uma localização como um ponto de gerenciamento.
     * 
     * @param string $namespace Um nome para conxtextualizar as chamadas
     * @param string $realPath O caminho real dentro do sistema de arquivos
     * @return Filesystem
     */
    public function mount(?string $namespace = null, ?string $realPath = null) : Filesystem
    {
        if ($namespace !== null && $realPath !== null) {

            $adapter = new Adapter\Local($realPath);
            $this->mountFilesystem($namespace, new LeagueFilesystem($adapter));
        }

        return $this;
    }
}