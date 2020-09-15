<?php
declare(strict_types=1);

namespace Tests\Writer;

use MarkHelp\Writer\Menu;
use Tests\TestCase;

class MenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    private function fakeExtractedItems(string $urlPrefix = ''): array
    {
        $extractedItems = [
            // <ul>
            0 => [
                'label' => 'Home Page',
                'url'   => $urlPrefix . 'index.html'
            ],
            1 => [
                'label' => 'Página 1',
                'url'   => $urlPrefix . '01_page_one.html'
            ],
            2 => [
                'label' => 'Página 2',
                'url'   => $urlPrefix . '02-page-two.html'
            ],
            // </ul>

            // <ul>
            3 => [
                'label' => 'O Básico',
                'url'   => $urlPrefix . 'javascript:void(0);',
                'children' => [
                    // <ul>
                    0 => [
                        'label' => 'Página 3',
                        'url'   => $urlPrefix . '01_O_Básico/01-page-three.html'
                    ],
                    1 => [
                        'label' => 'Página 4',
                        'url'   => $urlPrefix . '01_O_Básico/02-page-four.html'
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
                        'url'   => $urlPrefix . '02-Avançado/01-page-five.html'
                    ],
                    1 => [
                        'label' => 'Página 6',
                        'url'   => $urlPrefix . '02-Avançado/02-page-six.html'
                    ],
                    2 => [
                        'label'    => 'Subfolder',
                        'url'      => 'javascript:void(0);',
                        'children' => [
                            // <ul>
                            0 => [
                                'label' => 'Página 8',
                                'url'   => $urlPrefix . '02-Avançado/03_Subfolder/page-eight.html'
                            ],
                            1 => [
                                'label' => 'Página 7',
                                'url'   => $urlPrefix . '02-Avançado/03_Subfolder/page-seven.html'
                            ]
                            // </ul>
                        ]
                    ] 
                    // </ul>
                ] // children
            ]
            // </ul>
        ];

        return $extractedItems;
    }

    /** @test */
    public function renderMenu()
    {
        $extractedItems = $this->fakeExtractedItems();

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
                '<li class="has-childs">',
                    '<a href="javascript:void(0);">Subfolder <i class="arrow down"></i></a>',
                    '<ul>',
                        '<li><a href="02-Avançado/03_Subfolder/page-eight.html">Página 8</a></li>',
                        '<li><a href="02-Avançado/03_Subfolder/page-seven.html">Página 7</a></li>',
                    '</ul>',
                '</li>',
            '</ul>',
            '',
        ], $htmlLines);
    }

    /** @test */
    public function renderMenuWithCurrentPage()
    {
        $extractedItems = $this->fakeExtractedItems();

        $menu = new Menu($extractedItems, '01_page_one.html');

        $html = $menu->toHtml();
        $html = explode("\n", $html);
        $htmlLines = array_map(function($item){ return trim($item); }, $html);

        $this->assertEquals([
            '',
            '<ul>',
                '<li><a href="index.html">Home Page</a></li>',
                '<li class="selected"><a href="01_page_one.html">Página 1</a></li>',
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
                '<li class="has-childs">',
                    '<a href="javascript:void(0);">Subfolder <i class="arrow down"></i></a>',
                    '<ul>',
                        '<li><a href="02-Avançado/03_Subfolder/page-eight.html">Página 8</a></li>',
                        '<li><a href="02-Avançado/03_Subfolder/page-seven.html">Página 7</a></li>',
                    '</ul>',
                '</li>',
            '</ul>',
            '',
        ], $htmlLines);
    }

    /** @test */
    public function renderMenuWithCurrentPageWithPrefix()
    {
        $extractedItems = $this->fakeExtractedItems('../../');

        $menu = new Menu($extractedItems, '../../01_page_one.html');

        $html = $menu->toHtml();
        $html = explode("\n", $html);
        $htmlLines = array_map(function($item){ return trim($item); }, $html);

        $this->assertEquals([
            '',
            '<ul>',
                '<li><a href="../../index.html">Home Page</a></li>',
                '<li class="selected"><a href="../../01_page_one.html">Página 1</a></li>',
                '<li><a href="../../02-page-two.html">Página 2</a></li>',
            '</ul>',
            '',
            '<h2>O Básico</h2>',
            '',
            '<ul>',
                '<li><a href="../../01_O_Básico/01-page-three.html">Página 3</a></li>',
                '<li><a href="../../01_O_Básico/02-page-four.html">Página 4</a></li>',
            '</ul>',
            '',
            '<h2>Avançado</h2>',
            '',
            '<ul>',
                '<li><a href="../../02-Avançado/01-page-five.html">Página 5</a></li>',
                '<li><a href="../../02-Avançado/02-page-six.html">Página 6</a></li>',
                '<li class="has-childs">',
                    '<a href="javascript:void(0);">Subfolder <i class="arrow down"></i></a>',
                    '<ul>',
                        '<li><a href="../../02-Avançado/03_Subfolder/page-eight.html">Página 8</a></li>',
                        '<li><a href="../../02-Avançado/03_Subfolder/page-seven.html">Página 7</a></li>',
                    '</ul>',
                '</li>',
            '</ul>',
            '',
        ], $htmlLines);
    }

    /** @test */
    public function renderMenuWithCurrentPageInSubmenu()
    {
        $extractedItems = $this->fakeExtractedItems();

        $menu = new Menu($extractedItems, '02-Avançado/03_Subfolder/page-eight.html');

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
                '<li class="has-childs open">',
                    '<a href="javascript:void(0);">Subfolder <i class="arrow up"></i></a>',
                    '<ul>',
                        '<li class="selected"><a href="02-Avançado/03_Subfolder/page-eight.html">Página 8</a></li>',
                        '<li><a href="02-Avançado/03_Subfolder/page-seven.html">Página 7</a></li>',
                    '</ul>',
                '</li>',
            '</ul>',
            '',
        ], $htmlLines);
    }
}