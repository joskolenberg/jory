<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Exceptions\JoryException;

class ArrayParserFilterTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_empty_filter_which_results_in_the_filter_being_null_in_jory()
    {
        $parser = new ArrayParser([
            'filter' => [],
        ]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getFilter());
    }

    /** @test */
    public function it_can_parse_no_filter_which_results_in_the_filter_being_null_in_jory()
    {
        $parser = new ArrayParser([]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getFilter());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field' => 'name',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertNull($jory->getFilter()->getOperator());
        $this->assertNull($jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_operator()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field' => 'name',
                'operator' => '=',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertNull($jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_a_name_operator_and_data()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field' => 'name',
                'operator' => '=',
                'data' => 'John',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_data()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field' => 'name',
                'data' => 'John',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertNull($jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_groupAnd_filter()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_and' => [
                    [
                        'field' => 'first_name',
                        'data' => 'John',
                    ],
                    [
                        'field' => 'last_name',
                        'data' => 'Lennon',
                    ],
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('John', $filter->getByIndex(0)->data);
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Lennon', $filter->getByIndex(1)->data);
    }

    /** @test */
    public function it_can_parse_a_groupOr_filter()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_or' => [
                    [
                        'field' => 'first_name',
                        'data' => 'John',
                    ],
                    [
                        'field' => 'last_name',
                        'data' => 'Lennon',
                    ],
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupOrFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('John', $filter->getByIndex(0)->data);
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Lennon', $filter->getByIndex(1)->data);
    }

    /** @test */
    public function it_can_handle_grouped_filters()
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
        ]);

        $jory = $parser->getJory();
        $filter = $jory->getFilter();

        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals(3, count($filter));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(0));
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('Eric', $filter->getByIndex(0)->data);
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(1));
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Clapton', $filter->getByIndex(1)->data);
        $this->assertInstanceOf(GroupOrFilter::class, $filter->getByIndex(2));
        $this->assertEquals(2, count($filter->getByIndex(2)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(0));
        $this->assertEquals('band', $filter->getByIndex(2)->getByIndex(0)->field);
        $this->assertEquals('in', $filter->getByIndex(2)->getByIndex(0)->operator);
        $this->assertEquals(['beatles', 'stones'], $filter->getByIndex(2)->getByIndex(0)->data);
        $this->assertInstanceOf(GroupAndFilter::class, $filter->getByIndex(2)->getByIndex(1));
        $this->assertEquals(2, count($filter->getByIndex(2)->getByIndex(1)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(0));
        $this->assertEquals('project', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->field);
        $this->assertEquals('like', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->operator);
        $this->assertEquals('Cream', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->data);
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(1));
        $this->assertEquals('drummer', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->field);
        $this->assertNull($filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->operator);
        $this->assertEquals('Ginger Baker', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->data);
    }

    /** @test */
    public function it_will_only_parse_the_allowed_data()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_or' => [
                    [
                        'field' => 'first_name',
                        'data' => 'John',
                        'not_valid' => 'Testing',
                    ],
                    [
                        'field' => 'last_name',
                        'data' => 'Lennon',
                    ],
                ],
                'also_not_valid' => 'not parsed',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals([
            'filter' => [
                'group_or' => [
                    [
                        'field' => 'first_name',
                        'data' => 'John',
                    ],
                    [
                        'field' => 'last_name',
                        'data' => 'Lennon',
                    ],
                ],
            ],
        ], $jory->toArray(false));
    }

    /** @test */
    public function it_throws_an_exception_when_the_validator_fails()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter should contain one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: filter)');
        (new ArrayParser([
            'filter' => [
                'group_ord' => [
                    [
                        'field' => 'first_name',
                        'data' => 'John',
                        'not_valid' => 'Testing',
                    ],
                    [
                        'field' => 'last_name',
                        'data' => 'Lennon',
                    ],
                ],
                'also_not_valid' => 'not parsed',
            ],
        ]))->getJory();
    }

    /** @test */
    public function it_converts_a_string_to_a_single_item_array_so_a_string_can_be_passed_when_there_should_only_be_filtered_on_a_single_boolean_filter()
    {
        $parser = new ArrayParser([
            'filter' => 'is_active'
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('is_active', $jory->getFilter()->getField());
        $this->assertNull($jory->getFilter()->getOperator());
        $this->assertNull($jory->getFilter()->getData());
    }

    /** @test */
    public function it_converts_a_string_to_a_single_item_array_so_a_string_can_be_passed_when_there_should_only_be_filtered_on_a_single_boolean_filter_in_a_relation()
    {
        $parser = new ArrayParser([
            'relations' => [
                'user' => [
                    'filter' => 'is_active'
                ]
            ]
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getRelations()[0]->getJory()->getFilter());
        $this->assertEquals('is_active', $jory->getRelations()[0]->getJory()->getFilter()->getField());
        $this->assertNull($jory->getRelations()[0]->getJory()->getFilter()->getOperator());
        $this->assertNull($jory->getRelations()[0]->getJory()->getFilter()->getData());
    }
}
