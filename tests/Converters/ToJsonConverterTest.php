<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 11:48.
 */

namespace JosKolenberg\Jory\Tests\Converters;

use JosKolenberg\Jory\Converters\ToJsonConverter;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Support\Filter;
use PHPUnit\Framework\TestCase;

class ToJsonConverterTest extends TestCase
{
    /** @test */
    public function it_can_convert_a_jory_object_to_a_minified_array()
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
                                'field'    => 'band',
                                'operator' => 'in',
                                'value'    => ['beatles', 'stones'],
                            ],
                            [
                                'group_and' => [
                                    [
                                        'field'    => 'project',
                                        'operator' => 'like',
                                        'value'    => 'Cream',
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
        ]);

        $jory = $parser->getJory();

        $converter = new ToJsonConverter($jory);

        $this->assertEquals('{"flt":{"and":[{"f":"first_name","v":"Eric"},{"f":"last_name","v":"Clapton"},{"or":[{"f":"band","o":"in","v":["beatles","stones"]},{"and":[{"f":"project","o":"like","v":"Cream"},{"f":"drummer","v":"Ginger Baker"}]}]}]}}', $converter->get());
    }

    /** @test */
    public function it_can_convert_a_jory_object_to_an_array()
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
                                'field'    => 'band',
                                'operator' => 'in',
                                'value'    => ['beatles', 'stones'],
                            ],
                            [
                                'group_and' => [
                                    [
                                        'field'    => 'project',
                                        'operator' => 'like',
                                        'value'    => 'Cream',
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
        ]);

        $jory = $parser->getJory();

        $converter = new ToJsonConverter($jory, false);

        $this->assertEquals('{"filter":{"group_and":[{"field":"first_name","value":"Eric"},{"field":"last_name","value":"Clapton"},{"group_or":[{"field":"band","operator":"in","value":["beatles","stones"]},{"group_and":[{"field":"project","operator":"like","value":"Cream"},{"field":"drummer","value":"Ginger Baker"}]}]}]}}', $converter->get());
    }
}