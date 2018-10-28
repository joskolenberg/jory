<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Exceptions\JoryException;

class ArrayParserFieldsTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_omitted_fields_parameter()
    {
        $parser = new ArrayParser([]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getFields());
    }

    /** @test */
    public function it_can_parse_a_nulled_fields_parameter()
    {
        $parser = new ArrayParser([
            'fields' => null,
        ]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getFields());
    }

    /** @test */
    public function it_can_parse_a_fields_array_with_an_empty_array()
    {
        $parser = new ArrayParser([
            'fields' => [],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals([], $jory->getFields());
    }

    /** @test */
    public function it_can_parse_a_fields_array_with_a_single_field()
    {
        $parser = new ArrayParser([
            'fields' => [
                'first_name',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals(['first_name'], $jory->getFields());
    }

    /** @test */
    public function it_can_parse_a_fields_array_with_multiple_fields()
    {
        $parser = new ArrayParser([
            'fields' => [
                'first_name',
                'last_name',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals(['first_name', 'last_name'], $jory->getFields());
    }

    /** @test */
    public function it_throws_an_exception_when_invalid_data_is_passed()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The fields parameter must be an array. (Location: fields)');

        (new ArrayParser([
            'fields' => 'this_is_not_an_array',
        ]))->getJory();
    }

    /** @test */
    public function it_throws_an_exception_when_invalid_data_is_passed_on_a_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The fields parameter can only contain strings. (Location: users.fields.1)');

        (new ArrayParser([
            'rlt' => [
                'users' => [
                    'fields' => [
                        'valid',
                        [
                            'not',
                            'valid',
                        ],
                    ],
                ],
            ],
        ]))->getJory();
    }
}
