<?php
declare(strict_types=1);

namespace Tests\Writer;

use Exception;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Loader;
use MarkHelp\Writer\Menu;
use MarkHelp\Writer\Page;
use Tests\TestCase;

class MenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function renderMenu()
    {
        $extractedItems = [
            // <ul>
            0 => [
                'label' => 'Home Page',
                'url'   => 'index.html'
            ],
            1 => [
                'label' => 'Página 1',
                'url'   => '01_page_one.html'
            ],
            2 => [
                'label' => 'Página 2',
                'url'   => '02-page-two.html'
            ],
            // </ul>

            // <ul>
            3 => [
                'label' => 'O Básico',
                'url'   => 'javascript:void(0);',
                'children' => [
                    // <ul>
                    0 => [
                        'label' => 'Página 3',
                        'url'   => '01_O_Básico/01-page-three.html'
                    ],
                    1 => [
                        'label' => 'Página 4',
                        'url'   => '01_O_Básico/02-page-four.html'
                    ]
                    // </ul>
                ]
            ],
            // </ul>

            // <ul>
            4 => [
                'label'    => 'Avançado',
                'url'      => 'javascript:void(0);',
                'children' => [
                    // <ul>
                    0 => [
                        'label' => 'Página 5',
                        'url'   => '02-Avançado/01-page-five.html'
                    ],
                    1 => [
                        'label' => 'Página 6',
                        'url'   => '02-Avançado/02-page-six.html'
                    ],
                    2 => [
                        'label'    => 'Subfolder',
                        'url'      => 'javascript:void(0);',
                        'children' => [
                            // <ul>
                            0 => [
                                'label' => 'Página 8',
                                'url'   =>  '02-Avançado/03_Subfolder/page-eight.html'
                            ],
                            1 => [
                                'label' => 'Página 7',
                                'url'   => '02-Avançado/03_Subfolder/page-seven.html'
                            ]
                            // </ul>
                        ]
                    ] 
                    // </ul>
                ] // children
            ]
            // </ul>
        ];

        $menu = new Menu($extractedItems);

        $html = $menu->toHtml();
        $html = explode("\n", $html);
        $htmlLines = array_map(function($item){ return trim($item); }, $html);

        $this->assertEquals([
            '',
            '<ul>',
                '<li><a href="index.html">Home Page</a></li>',
                '<li><a href="01_page_one.html">Página 1</a></li>',
                '<li><a href="02-page-two.html">Página 2</a></li>',
            '</ul>',
            '',
            '<h2>O Básico</h2>',
            '',
            '<ul>',
                '<li><a href="01_O_Básico/01-page-three.html">Página 3</a></li>',
                '<li><a href="01_O_Básico/02-page-four.html">Página 4</a></li>',
            '</ul>',
            '',
            '<h2>Avançado</h2>',
            '',
            '<ul>',
                '<li><a href="02-Avançado/01-page-five.html">Página 5</a></li>',
                '<li><a href="02-Avançado/02-page-six.html">Página 6</a></li>',
                '<li>',
                    '<a href="javascript:void(0);">Subfolder</a>',
                    '<ul>',
                        '<li><a href="02-Avançado/03_Subfolder/page-eight.html">Página 8</a></li>',
                        '<li><a href="02-Avançado/03_Subfolder/page-seven.html">Página 7</a></li>',
                    '</ul>',
                '</li>',
            '</ul>',
            '',
        ], $htmlLines);
    }
}