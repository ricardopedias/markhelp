<?php
declare(strict_types=1);

namespace MarkHelp;

class Reader extends Handle
{
    private $info = [
        'logo.src'         => __DIR__ . '/themes/assets/logo.png',
        'logo.status'      => 'active',
        'project.name'     => 'Mark Help',
        'project.slogan'   => 'Gerador de documentação',
        'current.page'     => '',
        'github.url'       => 'https://github.com/ricardopedias/markhelp',
        'github.fork'      => 'active',
        'copy.name'        => 'Open Source',
        'copy.url'         => 'http://www.ricardopdias.com.br',
        'design.by'        => 'Design by',
        'designer.name'    => 'Ricardo Pereira',
        'designer.url'     => 'http://www.ricardopdias.com.br',
        'favicon'          => __DIR__ . '/themes/assets/favicon.ico',
        'apple.touch.icon' => __DIR__ . '/themes/assets/apple-touch-icon-precomposed.png'
    ];

    /**
     * Constrói um leitor de arquivos markdown.
     * 
     * @param string $path Diretório contendo arquivos markdown
     */
    public function __construct(string $path)
    {
        $this->setPathBase($path);
    }

    public function setInfo(array $list)
    {
        $this->info = array_merge($this->info, $list);
        return $this;
    }

    public function info()
    {
        return $this->info;
    }

    /**
     * Lé os arquivos markdown contidos no ditretório base
     * 
     * @param string $path
     */
    public function load()
    {
        $list = $this->filesystem()->listContents('./', true);

        foreach($list as $item) {

            if ($item['type'] === 'dir') {
                continue;
            }

            if ($item['path'] === 'index.md') {
                $this->structure['index'] = $item['path'];
                continue;
            }

            if ($item['path'] === 'menu.md') {
                $this->structure['menu'] = $item['path'];
                continue;
            }

            if (substr($item['path'], -2) === 'md') {
                $this->structure['pages'][] = $item['path'];
            }
        }
    }
}