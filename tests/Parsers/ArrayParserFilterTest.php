<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 09:16.
 */

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
            'flt' => [],
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
            'flt' => [
                'f' => 'name',
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
            'flt' => [
                'f' => 'name',
                'o' => '=',
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
            'flt' => [
                'f' => 'name',
                'o' => '=',
                'd' => 'John',
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
            'flt' => [
                'f' => 'name',
                'd' => 'John',
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
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->getField());
        $this->assertEquals('John', $filter->getByIndex(0)->getData());
        $this->assertEquals('last_name', $filter->getByIndex(1)->getField());
        $this->assertEquals('Lennon', $filter->getByIndex(1)->getData());
    }

    /** @test */
    public function it_can_parse_a_groupOr_filter()
    {
        $parser = new ArrayParser([
            'flt' => [
                'or' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupOrFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->getField());
        $this->assertEquals('John', $filter->getByIndex(0)->getData());
        $this->assertEquals('last_name', $filter->getByIndex(1)->getField());
        $this->assertEquals('Lennon', $filter->getByIndex(1)->getData());
    }

    /** @test */
    public function it_can_handle_grouped_filters()
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
        ]);

        $jory = $parser->getJory();
        $filter = $jory->getFilter();

        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals(3, count($filter));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(0));
        $this->assertEquals('first_name', $filter->getByIndex(0)->getField());
        $this->assertEquals('Eric', $filter->getByIndex(0)->getData());
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(1));
        $this->assertEquals('last_name', $filter->getByIndex(1)->getField());
        $this->assertEquals('Clapton', $filter->getByIndex(1)->getData());
        $this->assertInstanceOf(GroupOrFilter::class, $filter->getByIndex(2));
        $this->assertEquals(2, count($filter->getByIndex(2)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(0));
        $this->assertEquals('band', $filter->getByIndex(2)->getByIndex(0)->getField());
        $this->assertEquals('in', $filter->getByIndex(2)->getByIndex(0)->getOperator());
        $this->assertEquals(['beatles', 'stones'], $filter->getByIndex(2)->getByIndex(0)->getData());
        $this->assertInstanceOf(GroupAndFilter::class, $filter->getByIndex(2)->getByIndex(1));
        $this->assertEquals(2, count($filter->getByIndex(2)->getByIndex(1)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(0));
        $this->assertEquals('project', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->getField());
        $this->assertEquals('like', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->getOperator());
        $this->assertEquals('Cream', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->getData());
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(1));
        $this->assertEquals('drummer', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->getField());
        $this->assertNull($filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->getOperator());
        $this->assertEquals('Ginger Baker', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->getData());
    }

    /** @test */
    public function it_throws_an_exception_when_the_validator_fails_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain more than one of the fields "f" (field), "and" (AND group) or "or" (OR group). (Location: flt(or).0)');
        (new ArrayParser([
            'flt' => [
                'or' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                        'and' => [],
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                ],
            ],
        ]))->getJory();
    }

    /** @test */
    public function it_throws_an_exception_when_the_validator_fails_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('Unknown key "not_valid" in Jory Query. (Location: flt(or).0)');
        (new ArrayParser([
            'flt' => [
                'or' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                        'not_valid' => 'Testing',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                ],
            ],
        ]))->getJory();
    }

    /** @test */
    public function it_converts_a_string_to_a_single_item_array_so_a_string_can_be_passed_when_there_should_only_be_filtered_on_a_single_boolean_filter()
    {
        $parser = new ArrayParser([
            'flt' => 'is_active'
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
            'rlt' => [
                'user' => [
                    'flt' => 'is_active'
                ]
            ]
        ]);
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getRelations()[0]->getJory()->getFilter());
        $this->assertEquals('is_active', $jory->getRelations()[0]->getJory()->getFilter()->getField());
        $this->assertNull($jory->getRelations()[0]->getJory()->getFilter()->getOperator());
        $this->assertNull($jory->getRelations()[0]->getJory()->getFilter()->getData());
    }}
