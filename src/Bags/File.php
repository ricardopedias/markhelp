<?php

declare(strict_types=1);

namespace MarkHelp\Bags;

class File extends Bag
{
    public function __construct()
    {
        $this->addParams([
            'type'         => 'page',
            'assetsPrefix' => './',
            'pathSearch'   => 'none.md',
            'pathReplace'  => 'none.html',
        ]);
    }
}
