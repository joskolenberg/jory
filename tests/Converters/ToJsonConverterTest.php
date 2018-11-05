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
use JosKolenberg\Jory\Converters\ToJsonConverter;

class ToJsonConverterTest extends TestCase
{
    /** @test */
    public function it_can_convert_a_jory_object_to_minified_json()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_and' => [
                    [
                        'field' => 'first_name',
                        'data' => 'Eric',
                    ],
                    [
                        'field' => 'last_name',
                        'data' => 'Clapton',
                    ],
                    [
                        'group_or' => [
                            [
                                'field' => 'band',
                                'operator' => 'in',
                                'data' => ['beatles', 'stones'],
                            ],
                            [
                                'group_and' => [
                                    [
                                        'field' => 'project',
                                        'operator' => 'like',
                                        'data' => 'Cream',
                                    ],
                                    [
                                        'field' => 'drummer',
                                        'data' => 'Ginger Baker',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'sorts' => [
                '-year',
            ],
            'relations' => [
                'users' => [
                    'fields' => [
                        'first_name',
                        'last_name',
                    ],
                    'offset' => 100,
                    'limit' => 50,
                    'filter' => [
                        'field' => 'active',
                        'operator' => '=',
                        'data' => true,
                    ],
                    'sorts' => [
                        'name',
                        '-id',
                    ],
                ],
            ],
        ]);

        $jory = $parser->getJory();

        $converter = new ToJsonConverter($jory);

        $this->assertEquals('{"flt":{"and":[{"f":"first_name","d":"Eric"},{"f":"last_name","d":"Clapton"},{"or":[{"f":"band","o":"in","d":["beatles","stones"]},{"and":[{"f":"project","o":"like","d":"Cream"},{"f":"drummer","d":"Ginger Baker"}]}]}]},"srt":["-year"],"rlt":{"users":{"flt":{"f":"active","o":"=","d":true},"srt":["name","-id"],"ofs":100,"lmt":50,"fld":["first_name","last_name"]}}}', $converter->get());
    }

    /** @test */
    public function it_can_convert_a_jory_object_to_json()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_and' => [
                    [
                        'field' => 'first_name',
                        'data' => 'Eric',
                    ],
                    [
                        'field' => 'last_name',
                        'data' => 'Clapton',
                    ],
                    [
                        'group_or' => [
                            [
                                'field' => 'band',
                                'operator' => 'in',
                                'data' => ['beatles', 'stones'],
                            ],
                            [
                                'group_and' => [
                                    [
                                        'field' => 'project',
                                        'operator' => 'like',
                                        'data' => 'Cream',
                                    ],
                                    [
                                        'field' => 'drummer',
                                        'data' => 'Ginger Baker',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'sorts' => [
                '-year',
            ],
            'relations' => [
                'users' => [
                    'filter' => [
                        'field' => 'active',
                        'operator' => '=',
                        'data' => true,
                    ],
                    'offset' => 100,
                    'limit' => 50,
                    'sorts' => [
                        'name',
                        '-id',
                    ],
                    'fields' => [
                        'first_name',
                        'last_name',
                    ],
                ],
            ],
        ]);

        $jory = $parser->getJory();

        $converter = new ToJsonConverter($jory, false);

        $this->assertEquals('{"filter":{"group_and":[{"field":"first_name","data":"Eric"},{"field":"last_name","data":"Clapton"},{"group_or":[{"field":"band","operator":"in","data":["beatles","stones"]},{"group_and":[{"field":"project","operator":"like","data":"Cream"},{"field":"drummer","data":"Ginger Baker"}]}]}]},"sorts":["-year"],"relations":{"users":{"filter":{"field":"active","operator":"=","data":true},"sorts":["name","-id"],"offset":100,"limit":50,"fields":["first_name","last_name"]}}}', $converter->get());
    }

    /** @test */
    public function it_can_convert_an_empty_jory_object_to_json()
    {
        $parser = new ArrayParser([]);

        $jory = $parser->getJory();

        $converter = new ToJsonConverter($jory, false);

        $this->assertEquals('{}', $converter->get());
    }
}
