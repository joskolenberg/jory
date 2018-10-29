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
                        'value' => 'Eric',
                    ],
                    [
                        'field' => 'last_name',
                        'value' => 'Clapton',
                    ],
                    [
                        'group_or' => [
                            [
                                'field' => 'band',
                                'operator' => 'in',
                                'value' => ['beatles', 'stones'],
                            ],
                            [
                                'group_and' => [
                                    [
                                        'field' => 'project',
                                        'operator' => 'like',
                                        'value' => 'Cream',
                                    ],
                                    [
                                        'field' => 'drummer',
                                        'value' => 'Ginger Baker',
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
                        'value' => true,
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

        $this->assertEquals('{"flt":{"and":[{"f":"first_name","v":"Eric"},{"f":"last_name","v":"Clapton"},{"or":[{"f":"band","o":"in","v":["beatles","stones"]},{"and":[{"f":"project","o":"like","v":"Cream"},{"f":"drummer","v":"Ginger Baker"}]}]}]},"srt":["-year"],"rlt":{"users":{"flt":{"f":"active","o":"=","v":true},"srt":["name","-id"],"ofs":100,"lmt":50,"fld":["first_name","last_name"]}}}', $converter->get());
    }

    /** @test */
    public function it_can_convert_a_jory_object_to_json()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_and' => [
                    [
                        'field' => 'first_name',
                        'value' => 'Eric',
                    ],
                    [
                        'field' => 'last_name',
                        'value' => 'Clapton',
                    ],
                    [
                        'group_or' => [
                            [
                                'field' => 'band',
                                'operator' => 'in',
                                'value' => ['beatles', 'stones'],
                            ],
                            [
                                'group_and' => [
                                    [
                                        'field' => 'project',
                                        'operator' => 'like',
                                        'value' => 'Cream',
                                    ],
                                    [
                                        'field' => 'drummer',
                                        'value' => 'Ginger Baker',
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
                        'value' => true,
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

        $this->assertEquals('{"filter":{"group_and":[{"field":"first_name","value":"Eric"},{"field":"last_name","value":"Clapton"},{"group_or":[{"field":"band","operator":"in","value":["beatles","stones"]},{"group_and":[{"field":"project","operator":"like","value":"Cream"},{"field":"drummer","value":"Ginger Baker"}]}]}]},"sorts":["-year"],"relations":{"users":{"filter":{"field":"active","operator":"=","value":true},"sorts":["name","-id"],"offset":100,"limit":50,"fields":["first_name","last_name"]}}}', $converter->get());
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
