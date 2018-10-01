<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Support\GroupOrFilter;
use PHPUnit\Framework\TestCase;

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
        $this->assertNull($jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_operator()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field'    => 'name',
                'operator' => '=',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertNull($jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_a_name_operator_and_value()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field'    => 'name',
                'operator' => '=',
                'value'    => 'John',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_value()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field' => 'name',
                'value' => 'John',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertNull($jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_groupAnd_filter()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_and' => [
                    [
                        'field' => 'first_name',
                        'value' => 'John',
                    ],
                    [
                        'field' => 'last_name',
                        'value' => 'Lennon',
                    ],
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('John', $filter->getByIndex(0)->value);
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Lennon', $filter->getByIndex(1)->value);
    }

    /** @test */
    public function it_can_parse_a_groupOr_filter()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_or' => [
                    [
                        'field' => 'first_name',
                        'value' => 'John',
                    ],
                    [
                        'field' => 'last_name',
                        'value' => 'Lennon',
                    ],
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupOrFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('John', $filter->getByIndex(0)->value);
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Lennon', $filter->getByIndex(1)->value);
    }

    /** @test */
    public function it_can_handle_grouped_filters()
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
        $filter = $jory->getFilter();

        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals(3, count($filter));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(0));
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('Eric', $filter->getByIndex(0)->value);
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(1));
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Clapton', $filter->getByIndex(1)->value);
        $this->assertInstanceOf(GroupOrFilter::class, $filter->getByIndex(2));
        $this->assertEquals(2, count($filter->getByIndex(2)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(0));
        $this->assertEquals('band', $filter->getByIndex(2)->getByIndex(0)->field);
        $this->assertEquals('in', $filter->getByIndex(2)->getByIndex(0)->operator);
        $this->assertEquals(['beatles', 'stones'], $filter->getByIndex(2)->getByIndex(0)->value);
        $this->assertInstanceOf(GroupAndFilter::class, $filter->getByIndex(2)->getByIndex(1));
        $this->assertEquals(2, count($filter->getByIndex(2)->getByIndex(1)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(0));
        $this->assertEquals('project', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->field);
        $this->assertEquals('like', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->operator);
        $this->assertEquals('Cream', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->value);
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(1));
        $this->assertEquals('drummer', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->field);
        $this->assertNull($filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->operator);
        $this->assertEquals('Ginger Baker', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->value);
    }

    /** @test */
    public function it_will_only_parse_the_allowed_data()
    {
        $parser = new ArrayParser([
            'filter' => [
                'group_or' => [
                    [
                        'field'     => 'first_name',
                        'value'     => 'John',
                        'not_valid' => 'Testing',
                    ],
                    [
                        'field' => 'last_name',
                        'value' => 'Lennon',
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
                        'value' => 'John',
                    ],
                    [
                        'field' => 'last_name',
                        'value' => 'Lennon',
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
        new ArrayParser([
            'filter' => [
                'group_ord' => [
                    [
                        'field'     => 'first_name',
                        'value'     => 'John',
                        'not_valid' => 'Testing',
                    ],
                    [
                        'field' => 'last_name',
                        'value' => 'Lennon',
                    ],
                ],
                'also_not_valid' => 'not parsed',
            ],
        ]);
    }
}
