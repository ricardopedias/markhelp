<?php
declare(strict_types=1);

namespace Tests\Writer;

use Exception;
use MarkHelp\Writer\TreeBuilder;
use Tests\TestCase;

class TreeListTest extends TestCase
{
     /** @test */
     public function flatListFromPathsInvalidPath()
     {
         $this->expectException(Exception::class);
         $this->expectExceptionMessage('The paths list is poorly formatted');
 
        $pathsList = [
            "01-page-one.md",
            new \ArrayObject([]),
            "index.md",
        ];
        
        $builder = new TreeBuilder();
        $flatList = $builder->flatListFromPaths($pathsList);
     }

    /** @test */
    public function flatListFromPathsOneLevel()
    {
        $pathsList = [
            "01-page-one.md",
            "02-page-two.md",
            "index.md",
        ];
        
        $builder = new TreeBuilder();
        $flatList = $builder->flatListFromPaths($pathsList);
        $this->assertEquals([
           0 => ['id' => 1,  'parent' => 0,  'path' => '01-page-one.md'],
           1 => ['id' => 2,  'parent' => 0,  'path' => '02-page-two.md'],
           2 => ['id' => 3,  'parent' => 0,  'path' => 'index.md'],
        ], $flatList);
    }

    /** @test */
    public function flatListFromPathsTwoLevels()
    {
        $pathsList = [
            "01-page-one.md",
            "02-page-two.md",
            "index.md",
            // 01_O_Básico
            "01_O_Básico/01-page-three.md",
            "01_O_Básico/02-page-four.md",
        ];

        $builder = new TreeBuilder();
        $flatList = $builder->flatListFromPaths($pathsList);
        $this->assertEquals([
           0 => ['id' => 1,  'parent' => 0,  'path' => '01-page-one.md'],
           1 => ['id' => 2,  'parent' => 0,  'path' => '02-page-two.md'],
           2 => ['id' => 3,  'parent' => 0,  'path' => 'index.md'],
           3 => ['id' => 4,  'parent' => 0,  'path' => '01_O_Básico'],
           4 => ['id' => 5,  'parent' => 4,  'path' => '01_O_Básico/01-page-three.md'],
           5 => ['id' => 6,  'parent' => 4,  'path' => '01_O_Básico/02-page-four.md'],
        ], $flatList);
    }

    /** @test */
    public function flatListFromPathsThreeLevels()
    {
        $pathsList = [
            "01-page-one.md",
            "02-page-two.md",
            "index.md",
            // 01_O_Básico
            "01_O_Básico/01-page-three.md",
            "01_O_Básico/02-page-four.md",
            // 02-Avançado
            "02-Avançado/01-page-five.md",
            "02-Avançado/02-page-six.md",
            // 03_Subfolder */
            "02-Avançado/03_Subfolder/page-eight.md",
            "02-Avançado/03_Subfolder/page-seven.md",
            // images
            "images/example.png",
        ];

        $builder = new TreeBuilder();
        $flatList = $builder->flatListFromPaths($pathsList);
        $this->assertEquals([
           0 => ['id' => 1,  'parent' => 0,  'path' => '01-page-one.md'],
           1 => ['id' => 2,  'parent' => 0,  'path' => '02-page-two.md'],
           2 => ['id' => 3,  'parent' => 0,  'path' => 'index.md'],
           3 => ['id' => 4,  'parent' => 0,  'path' => '01_O_Básico'],
           4 => ['id' => 5,  'parent' => 4,  'path' => '01_O_Básico/01-page-three.md'],
           5 => ['id' => 6,  'parent' => 4,  'path' => '01_O_Básico/02-page-four.md'],
           6 => ['id' => 7,  'parent' => 0,  'path' => '02-Avançado'],
           7 => ['id' => 8,  'parent' => 7,  'path' => '02-Avançado/01-page-five.md'],
           8 => ['id' => 9,  'parent' => 7,  'path' => '02-Avançado/02-page-six.md'],
           9 => ['id' => 10, 'parent' => 7,  'path' => '02-Avançado/03_Subfolder'],
           10 => ['id' => 11, 'parent' => 10, 'path' => '02-Avançado/03_Subfolder/page-eight.md'],
           11 => ['id' => 12, 'parent' => 10, 'path' => '02-Avançado/03_Subfolder/page-seven.md'],
           12 => ['id' => 13, 'parent' => 0,  'path' => 'images'],
           13 => ['id' => 14, 'parent' => 13, 'path' => 'images/example.png'],
        ], $flatList);
    }

    /** @test */
    public function flatListFromPathsStartBar()
    {
        $pathsList = [
            "/01-page-one.md",
            "/02-page-two.md",
            "/index.md",
            // 01_O_Básico
            "/01_O_Básico/01-page-three.md",
            "/01_O_Básico/02-page-four.md",
            // 02-Avançado
            "/02-Avançado/01-page-five.md",
            "/02-Avançado/02-page-six.md",
            // 03_Subfolder */
            "/02-Avançado/03_Subfolder/page-eight.md",
            "/02-Avançado/03_Subfolder/page-seven.md",
            // images
            "/images/example.png",
        ];

        $builder = new TreeBuilder();
        $flatList = $builder->flatListFromPaths($pathsList);
        $this->assertEquals([
           0 => ['id' => 1,  'parent' => 0,  'path' => '01-page-one.md'],
           1 => ['id' => 2,  'parent' => 0,  'path' => '02-page-two.md'],
           2 => ['id' => 3,  'parent' => 0,  'path' => 'index.md'],
           3 => ['id' => 4,  'parent' => 0,  'path' => '01_O_Básico'],
           4 => ['id' => 5,  'parent' => 4,  'path' => '01_O_Básico/01-page-three.md'],
           5 => ['id' => 6,  'parent' => 4,  'path' => '01_O_Básico/02-page-four.md'],
           6 => ['id' => 7,  'parent' => 0,  'path' => '02-Avançado'],
           7 => ['id' => 8,  'parent' => 7,  'path' => '02-Avançado/01-page-five.md'],
           8 => ['id' => 9,  'parent' => 7,  'path' => '02-Avançado/02-page-six.md'],
           9 => ['id' => 10, 'parent' => 7,  'path' => '02-Avançado/03_Subfolder'],
           10 => ['id' => 11, 'parent' => 10, 'path' => '02-Avançado/03_Subfolder/page-eight.md'],
           11 => ['id' => 12, 'parent' => 10, 'path' => '02-Avançado/03_Subfolder/page-seven.md'],
           12 => ['id' => 13, 'parent' => 0,  'path' => 'images'],
           13 => ['id' => 14, 'parent' => 13, 'path' => 'images/example.png'],
        ], $flatList);
    }

    /** @test */
    public function flatListFromPathsRandom()
    {
        $pathsList = [
            "02-Avançado/03_Subfolder/page-seven.md",
            "02-Avançado/01-page-five.md",
            // images
            "images/example.png",
            // 01_O_Básico
            "01_O_Básico/01-page-three.md",
            "02-page-two.md",
            // 02-Avançado
            "01_O_Básico/02-page-four.md",
            "index.md",
            "02-Avançado/02-page-six.md",
            // 03_Subfolder */
            "02-Avançado/03_Subfolder/page-eight.md",
            "01-page-one.md",
        ];
        
        $builder = new TreeBuilder();
        $flatList = $builder->flatListFromPaths($pathsList);
        $this->assertEquals([
           0 => ['id' => 1,  'parent' => 0,  'path' => '01-page-one.md'],
           1 => ['id' => 2,  'parent' => 0,  'path' => '02-page-two.md'],
           2 => ['id' => 3,  'parent' => 0,  'path' => 'index.md'],
           3 => ['id' => 4,  'parent' => 0,  'path' => '01_O_Básico'],
           4 => ['id' => 5,  'parent' => 4,  'path' => '01_O_Básico/01-page-three.md'],
           5 => ['id' => 6,  'parent' => 4,  'path' => '01_O_Básico/02-page-four.md'],
           6 => ['id' => 7,  'parent' => 0,  'path' => '02-Avançado'],
           7 => ['id' => 8,  'parent' => 7,  'path' => '02-Avançado/01-page-five.md'],
           8 => ['id' => 9,  'parent' => 7,  'path' => '02-Avançado/02-page-six.md'],
           9 => ['id' => 10, 'parent' => 7,  'path' => '02-Avançado/03_Subfolder'],
           10 => ['id' => 11, 'parent' => 10, 'path' => '02-Avançado/03_Subfolder/page-eight.md'],
           11 => ['id' => 12, 'parent' => 10, 'path' => '02-Avançado/03_Subfolder/page-seven.md'],
           12 => ['id' => 13, 'parent' => 0,  'path' => 'images'],
           13 => ['id' => 14, 'parent' => 13, 'path' => 'images/example.png'],
        ], $flatList);
    }

    /** @test */
    public function treeFromFlatList()
    {
        $flatList = [
            ['id' => 1, 'parent' => 0],
            ['id' => 2, 'parent' => 0],
            ['id' => 3, 'parent' => 2],
            ['id' => 4, 'parent' => 3],
            ['id' => 5, 'parent' => 0],
            ['id' => 6, 'parent' => 5],
            ['id' => 7, 'parent' => 2]
        ];

        $builder = new TreeBuilder();
        $this->assertEquals([
            1 => [
                "id"       => 1,
                "parent"   => 0,
                "children" => []
            ],
            2 => [
                "id"       => 2,
                "parent"   => 0,
                "children" => [
                    3 => [
                        "id" => 3,
                        "parent" => 2,
                        "children" => [
                            4 => [
                                "id" => 4,
                                "parent" => 3,
                                "children" => []
                            ]
                        ]
                    ],
                    7 => [
                        "id" => 7,
                        "parent" => 2,
                        "children" => []
                    ]
                ]
            ],
            5 => [
                "id"       => 5,
                "parent"   => 0,
                "children" => [
                    6 => [
                        "id"       => 6,
                        "parent"   => 5,
                        "children" => []
                    ]
                ]
            ]
        ], $builder->treeFromFlatList($flatList));
    }

    /** @test */
    public function treeFromFlatListExceptionInvalidId()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The flat list is poorly formatted');

        $flatList = [
            ['id' => new \ArrayObject([]), 'parent' => 0],
            ['id' => 2, 'parent' => 0],
        ];

        $builder = new TreeBuilder();
        $builder->treeFromFlatList($flatList);
    }

    /** @test */
    public function treeFromFlatListExceptionInvalidParent()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The flat list is poorly formatted');

        $flatList = [
            ['id' => 1, 'parent' => new \ArrayObject([])],
            ['id' => 2, 'parent' => 0],
        ];

        $builder = new TreeBuilder();
        $builder->treeFromFlatList($flatList);
    }

    /** @test */
    public function treeFromFlatListBuilded()
    {
        $pathsList = [
            "01-page-one.md",
            "02-page-two.md",
            "index.md",
            // 01_O_Básico
            "01_O_Básico/01-page-three.md",
            "01_O_Básico/02-page-four.md",
            // 02-Avançado
            "02-Avançado/01-page-five.md",
            "02-Avançado/02-page-six.md",
            // 03_Subfolder */
            "02-Avançado/03_Subfolder/page-eight.md",
            "02-Avançado/03_Subfolder/page-seven.md",
            // images
            "images/example.png",
        ];

        $builder = new TreeBuilder();
        $flatList = $builder->flatListFromPaths($pathsList);

        $this->assertEquals([
            1 => [
                "id"       => 1,
                "parent"   => 0,
                "path"     => '01-page-one.md',
                "children" => []
            ],
            2 => [
                "id"       => 2,
                "parent"   => 0,
                "path"     => '02-page-two.md',
                "children" => []
            ],
            3 => [
                "id"       => 3,
                "parent"   => 0,
                "path"     => 'index.md',
                "children" => []
            ],
            4 => [
                "id"       => 4,
                "parent"   => 0,
                "path"     => '01_O_Básico',
                "children" => [
                    5 => [
                        "id"       => 5,
                        "parent"   => 4,
                        "path"     => '01_O_Básico/01-page-three.md',
                        "children" => []
                    ],
                    6 => [
                        "id"       => 6,
                        "parent"   => 4,
                        "path"     => '01_O_Básico/02-page-four.md',
                        "children" => []
                    ],
                ]
            ],
            7 => [
                "id"       => 7,
                "parent"   => 0,
                "path"     => '02-Avançado',
                "children" => [
                    8 => [
                        "id"       => 8,
                        "parent"   => 7,
                        "path"     => '02-Avançado/01-page-five.md',
                        "children" => []
                    ],
                    9 => [
                        "id"       => 9,
                        "parent"   => 7,
                        "path"     => '02-Avançado/02-page-six.md',
                        "children" => []
                    ],
                    10 => [
                        "id"       => 10,
                        "parent"   => 7,
                        "path"     => '02-Avançado/03_Subfolder',
                        "children" => [
                            11 => [
                                "id"       => 11,
                                "parent"   => 10,
                                "path"     => '02-Avançado/03_Subfolder/page-eight.md',
                                "children" => []
                            ],
                            12 => [
                                "id"       => 12,
                                "parent"   => 10,
                                "path"     => '02-Avançado/03_Subfolder/page-seven.md',
                                "children" => []
                            ],
                        ]
                    ],
                ]
            ],
            13 => [
                "id"       => 13,
                "parent"   => 0,
                "path"     => 'images',
                "children" => [
                    14 => [
                        "id"       => 14,
                        "parent"   => 13,
                        "path"     => 'images/example.png',
                        "children" => []
                    ]
                ]
            ]
        ], $builder->treeFromFlatList($flatList));
        
    }
}