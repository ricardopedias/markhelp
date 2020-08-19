<?php

declare(strict_types=1);

namespace MarkHelp\Bags;

class Support extends Bag
{
    public function __construct()
    {
        $this->addParams([
            'mountPoint'  => 'origin',
            'supportPath' => 'none.css',
        ]);
    }
}
