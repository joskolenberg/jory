<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Exceptions\JoryException;

class ArrayParserSortTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_empty_sorts_array_which_results_in_the_sorts_array_being_empty_in_jory()
    {
        $parser = new ArrayParser([
            'sorts' => [],
        ]);
        $jory = $parser->getJory();
        $this->assertEmpty($jory->getSorts());
    }

    /** @test */
    public function it_can_parse_an_asc_sort()
    {
        $parser = new ArrayParser([
            'sorts' => [
                'name',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('name', $jory->getSorts()[0]->getField());
        $this->assertEquals('asc', $jory->getSorts()[0]->getOrder());
    }

    /** @test */
    public function it_can_parse_an_desc_sort()
    {
        $parser = new ArrayParser([
            'sorts' => [
                '-name',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('name', $jory->getSorts()[0]->getField());
        $this->assertEquals('desc', $jory->getSorts()[0]->getOrder());
    }

    /** @test */
    public function it_throws_an_exception_when_a_non_array_is_passed_as_sort()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The sorts parameter should be an array or string. (Location: sorts)');

        (new ArrayParser([
            'sorts' => 123324,
        ]))->getJory();
    }

    /** @test */
    public function it_converts_a_string_to_a_single_item_array_so_a_string_can_be_passed_when_the_should_only_be_sorted_on_a_single_field()
    {
        $parser = new ArrayParser([
            'sorts' => '-first_name'
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('first_name', $jory->getSorts()[0]->getField());
        $this->assertEquals('desc', $jory->getSorts()[0]->getOrder());
    }

    /** @test */
    public function it_converts_a_string_to_a_single_item_array_so_a_string_can_be_passed_when_the_should_only_be_sorted_on_a_single_field_in_a_relation()
    {
        $parser = new ArrayParser([
            'relations' => [
                'user' => [
                    'sorts' => 'first_name'
                ]
            ]
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('first_name', $jory->getRelations()[0]->getJory()->getSorts()[0]->getField());
        $this->assertEquals('asc', $jory->getRelations()[0]->getJory()->getSorts()[0]->getOrder());
    }
}
