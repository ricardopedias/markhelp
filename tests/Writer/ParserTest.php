<?php
declare(strict_types=1);

namespace Tests\Writer;

use MarkHelp\Reader\Release;
use MarkHelp\Writer\Parser;
use Tests\TestCase;

class ParserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanDestination();
    }

    /** @test */
    public function extractTitle()
    {
        $pagePath = $this->normalizePath($this->pathReleases . '/v4.0.0/01-page-one.md');
        $contents = file_get_contents($pagePath);
        $parser = new Parser();
        $this->assertEquals('Página 1', $parser->extractTitle($contents));
    }

    /** @test */
    public function extractTitleFromEmptyString()
    {
        $parser = new Parser();
        $this->assertEquals('', $parser->extractTitle(''));
    }

    /** @test */
    public function extractTitleNotInFirstLine()
    {
        $pagePath = $this->normalizePath($this->pathReleases . '/v4.0.0/01-page-one.md');
        $contents = file_get_contents($pagePath);
        $parser = new Parser();
        $this->assertEquals('Página 1', $parser->extractTitle("\n" . $contents));

        $pagePath = $this->normalizePath($this->pathReleases . '/v4.0.0/01-page-one.md');
        $contents = file_get_contents($pagePath);
        $parser = new Parser();
        $this->assertEquals('Página 1', $parser->extractTitle("\n\n\n" . $contents));
    }

    /** @test */
    public function extractTitleWithBeforeSpaces()
    {
        $pagePath = $this->normalizePath($this->pathReleases . '/v4.0.0/01-page-one.md');
        $contents = file_get_contents($pagePath);
        $parser = new Parser();
        $this->assertEquals('Página 1', $parser->extractTitle(" " . $contents));

        $pagePath = $this->normalizePath($this->pathReleases . '/v4.0.0/01-page-one.md');
        $contents = file_get_contents($pagePath);
        $parser = new Parser();
        $this->assertEquals('Página 1', $parser->extractTitle("     " . $contents));
    }

    /** @test */
    public function extractTitleNotInFirstLineWithBeforeSpaces()
    {
        $pagePath = $this->normalizePath($this->pathReleases . '/v4.0.0/01-page-one.md');
        $contents = file_get_contents($pagePath);
        $parser = new Parser();
        $this->assertEquals('Página 1', $parser->extractTitle("\n " . $contents));

        $pagePath = $this->normalizePath($this->pathReleases . '/v4.0.0/01-page-one.md');
        $contents = file_get_contents($pagePath);
        $parser = new Parser();
        $this->assertEquals('Página 1', $parser->extractTitle("\n\n\n     " . $contents));
    }

    /** @test */
    public function extractMenuItems()
    {
        $releasePath = $this->normalizePath($this->pathReleases . '/v4.0.0');
        $release = new Release('v4.0.0', $releasePath);
        $parser = new Parser();

        $list = $parser->extractMenuItems($release);

        $this->assertEquals([
            0 => [
                'label' => 'Home Page',
                'url'   => 'index.html'
            ],
            1 => [
                'label' => 'Página 1',
                'url'   => '01-page-one.html'
            ],
            2 => [
                'label' => 'Página 2',
                'url'   => '02-page-two.html'
            ],
            3 => [
                'label' => 'O Básico',
                'url'   => 'javascript:void(0);',
                'children' => [
                    0 => [
                        'label' => 'Página 3',
                        'url'   => '01_O_Básico/01-page-three.html'
                    ],
                    1 => [
                        'label' => 'Página 4',
                        'url'   => '01_O_Básico/02-page-four.html'
                    ]
                ] // children
            ],
            4 => [
                'label'    => 'Avançado',
                'url'      => 'javascript:void(0);',
                'children' => [
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
                            0 => [
                                'label' => 'Página 8',
                                'url'   =>  '02-Avançado/03_Subfolder/page-eight.html'
                            ],
                            1 => [
                                'label' => 'Página 7',
                                'url'   => '02-Avançado/03_Subfolder/page-seven.html'
                            ]
                        ] // children
                    ] // 2
                ] // children
            ] // 4
        ], $list);
    }
}