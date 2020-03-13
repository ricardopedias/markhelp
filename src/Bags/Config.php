<?php
declare(strict_types=1);

namespace MarkHelp\Bags;

use MarkHelp\App\Tools;

class Config extends Bag
{
    use Tools;

    public function __construct(string $pathRoot)
    {
        $this->addParams([
            'path.root'           => $pathRoot,
            'path.theme'          => $this->dirname(__DIR__) . '/Themes/default',
            'logo.status'         => true,
            'generate.phpindex'   => false,
            'project.name'        => 'Mark Help',
            'project.slogan'      => 'Gerador de documentação',
            'project.description' => 'Gerador de documentação feito em PHP',
            'current.page'        => '',
            'github.url'          => 'https://github.com/ricardopedias/markhelp',
            'github.fork'         => true,
            'copy.name'           => 'Open Source',
            'copy.url'            => 'http://www.ricardopdias.com.br',
            'created.by'          => 'Created by',
            'creator.name'        => 'Ricardo Pereira',
            'creator.url'         => 'http://www.ricardopdias.com.br',
            'support.document'    => null,
            'support.menu'        => null,
            'assets.styles'       => '{{theme}}/assets/styles.css',
            'assets.scripts'      => '{{theme}}/assets/scripts.js',
            'assets.logo.src'     => '{{theme}}/assets/logo.png',
            'assets.icon.favicon' => '{{theme}}/assets/favicon.ico',
            'assets.icon.apple'   => '{{theme}}/assets/apple-touch-icon-precomposed.png',
        ]);
    }
}