<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 11:48.
 */

namespace JosKolenberg\Jory\Tests\Converters;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Converters\ToArrayConverter;

class ToArrayConverterTest extends TestCase
{
    /** @test */
    public function it_can_convert_a_jory_object_to_an_array()
    {
        $parser = new ArrayParser([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'Eric',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Clapton',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'o' => 'in',
                                'd' => ['beatles', 'stones'],
                            ],
                            [
                                'and' => [
                                    [
                                        'f' => 'project',
                                        'o' => 'like',
                                        'd' => 'Cream',
                                    ],
                                    [
                                        'f' => 'drummer',
                                        'd' => 'Ginger Baker',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'srt' => [
                '-year',
            ],
            'rlt' => [
                'users' => [
                    'flt' => [
                        'f' => 'active',
                        'o' => '=',
                        'd' => true,
                    ],
                    'fld' => [
                        'first_name',
                        'last_name',
                    ],
                    'srt' => [
                        'name',
                        '-id',
                    ],
                    'ofs' => 100,
                    'lmt' => 50,
                ],
            ],
            'ofs' => 20,
            'lmt' => 5,
        ]);

        $jory = $parser->getJory();

        $converter = new ToArrayConverter($jory);

        $this->assertEquals([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'Eric',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Clapton',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'o' => 'in',
                                'd' => ['beatles', 'stones'],
                            ],
                            [
                                'and' => [
                                    [
                                        'f' => 'project',
                                        'o' => 'like',
                                        'd' => 'Cream',
                                    ],
                                    [
                                        'f' => 'drummer',
                                        'd' => 'Ginger Baker',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'srt' => [
                '-year',
            ],
            'rlt' => [
                'users' => [
                    'flt' => [
                        'f' => 'active',
                        'o' => '=',
                        'd' => true,
                    ],
                    'srt' => [
                        'name',
                        '-id',
                    ],
                    'ofs' => 100,
                    'lmt' => 50,
                    'fld' => [
                        'first_name',
                        'last_name',
                    ],
                ],
            ],
            'ofs' => 20,
            'lmt' => 5,
        ], $converter->get());
    }
}
