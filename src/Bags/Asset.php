<?php
declare(strict_types=1);

namespace MarkHelp\Bags;

class Asset extends Bag
{
    public function __construct()
    {
        $this->addParams([
            'assetType'     => 'builtin',           // builtin|custom
            'mountPoint'    => 'origin',
            'assetParam'    => 'assets.none',
            'assetPath'     => 'assets/none.css',
            'assetBasename' => 'none.css',
        ]);
    }
}