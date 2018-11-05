<?php

namespace JosKolenberg\Jory\Tests;

use JosKolenberg\Jory\Jory;
use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Sort;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\Relation;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Contracts\FilterInterface;

class JoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_and_get_a_filter_as_filter()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $filter = $jory->getFilter();

        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertInstanceOf(FilterInterface::class, $filter);
        $this->assertEquals('name', $filter->getField());
        $this->assertEquals('=', $filter->getoperator());
        $this->assertEquals('John', $filter->getData());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_a_group_and_filter_as_filter()
    {
        $jory = new Jory();
        $jory->setFilter(new GroupAndFilter());
        $filter = $jory->getFilter();

        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertInstanceOf(FilterInterface::class, $filter);
    }

    /**
     * @test
     */
    public function it_can_set_and_get_a_group_or_filter_as_filter()
    {
        $jory = new Jory();
        $jory->setFilter(new GroupOrFilter());
        $filter = $jory->getFilter();

        $this->assertInstanceOf(GroupOrFilter::class, $filter);
        $this->assertInstanceOf(FilterInterface::class, $filter);
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_filter_is_set()
    {
        $jory = new Jory();
        $this->assertNull($jory->getFilter());
    }

    /** @test */
    public function it_can_convert_itself_to_a_minified_array()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $this->assertEquals(['flt' => ['f' => 'name', 'o' => '=', 'd' => 'John']], $jory->toArray());
    }

    /** @test */
    public function it_can_convert_itself_to_an_array()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $this->assertEquals([
            'filter' => [
                'field' => 'name',
                'operator' => '=',
                'data' => 'John',
            ],
        ], $jory->toArray(false));
    }

    /** @test */
    public function it_can_convert_itself_to_minified_json()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $this->assertEquals('{"flt":{"f":"name","o":"=","d":"John"}}', $jory->toJson());
    }

    /** @test */
    public function it_can_convert_itself_to_json()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $this->assertEquals('{"filter":{"field":"name","operator":"=","data":"John"}}', $jory->toJson(false));
    }

    /** @test */
    public function it_can_add_relations_and_return_them_as_an_array()
    {
        $jory = new Jory();
        $jory->addRelation(new Relation('user'));

        $relations = $jory->getRelations();

        $this->assertCount(1, $relations);
        $this->assertInstanceOf(Relation::class, $relations[0]);
    }

    /** @test */
    public function it_can_add_relations_and_return_them_as_an_array_2()
    {
        $jory = new Jory();
        $jory->addRelation(new Relation('user'));
        $jory->addRelation(new Relation('user'));
        $jory->addRelation(new Relation('user'));

        $relations = $jory->getRelations();

        $this->assertCount(3, $relations);
        $this->assertInstanceOf(Relation::class, $relations[2]);
    }

    /** @test */
    public function it_can_add_sorts_and_return_them_as_an_array()
    {
        $jory = new Jory();
        $jory->addSort(new Sort('user'));

        $sorts = $jory->getSorts();

        $this->assertCount(1, $sorts);
        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertEquals('user', $sorts[0]->getField());
        $this->assertEquals('asc', $sorts[0]->getOrder());
    }

    /** @test */
    public function it_can_add_sorts_and_return_them_as_an_array_2()
    {
        $jory = new Jory();
        $jory->addSort(new Sort('user'));
        $jory->addSort(new Sort('car', 'asc'));
        $jory->addSort(new Sort('bike', 'desc'));

        $sorts = $jory->getSorts();

        $this->assertCount(3, $sorts);
        $this->assertInstanceOf(Sort::class, $sorts[2]);
        $this->assertEquals('user', $sorts[0]->getField());
        $this->assertEquals('asc', $sorts[0]->getOrder());
        $this->assertEquals('car', $sorts[1]->getField());
        $this->assertEquals('asc', $sorts[1]->getOrder());
        $this->assertEquals('bike', $sorts[2]->getField());
        $this->assertEquals('desc', $sorts[2]->getOrder());
    }

    /** @test */
    public function it_can_set_and_get_its_offset_value()
    {
        $jory = new Jory();
        $jory->setOffset(123);
        $this->assertEquals(123, $jory->getOffset());
    }

    /** @test */
    public function the_default_value_for_offset_is_null()
    {
        $jory = new Jory();
        $this->assertNull($jory->getOffset());
    }

    /** @test */
    public function it_can_set_null_value_for_offset()
    {
        $jory = new Jory();
        $jory->setOffset(null);
        $this->assertNull($jory->getOffset());
    }

    /** @test */
    public function it_can_set_and_get_its_limit_value()
    {
        $jory = new Jory();
        $jory->setLimit(123);
        $this->assertEquals(123, $jory->getLimit());
    }

    /** @test */
    public function the_default_value_for_limit_is_null()
    {
        $jory = new Jory();
        $this->assertNull($jory->getLimit());
    }

    /** @test */
    public function it_can_set_null_value_for_limit()
    {
        $jory = new Jory();
        $jory->setLimit(null);
        $this->assertNull($jory->getLimit());
    }

    /** @test */
    public function it_can_set_a_field()
    {
        $jory = new Jory();
        $jory->setFields(['testing']);

        $this->assertCount(1, $jory->getFields());
        $this->assertEquals('testing', $jory->getFields()[0]);
    }

    /** @test */
    public function it_can_set_multiple_fields()
    {
        $jory = new Jory();
        $jory->setFields(['testing', '123']);

        $this->assertCount(2, $jory->getFields());
        $this->assertEquals('testing', $jory->getFields()[0]);
        $this->assertEquals('123', $jory->getFields()[1]);
    }

    /** @test */
    public function it_can_set_the_fields_to_null()
    {
        $jory = new Jory();
        $jory->setFields(['testing', '123']);
        $jory->setFields(null);

        $this->assertNull($jory->getFields());
    }

    /** @test */
    public function when_no_fields_are_added_the_field_parameter_is_null()
    {
        $jory = new Jory();

        $this->assertNull($jory->getFields());
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_field_1()
    {
        $jory = new Jory();
        $jory->setFields([]);
        $this->assertFalse($jory->hasField('first_name'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_field_2()
    {
        $jory = new Jory();
        $jory->setFields(['last_name']);
        $this->assertFalse($jory->hasField('first_name'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_field_3()
    {
        $jory = new Jory();
        $jory->setFields([
            'first_name',
            'last_name',
            'full_name',
        ]);
        $this->assertTrue($jory->hasField('first_name'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_sort_1()
    {
        $jory = new Jory();
        $this->assertFalse($jory->hasSort('first_name'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_sort_2()
    {
        $jory = new Jory();
        $jory->addSort(new Sort('last_name', 'asc'));
        $this->assertFalse($jory->hasSort('first_name'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_sort_3()
    {
        $jory = new Jory();
        $jory->addSort(new Sort('first_name', 'asc'));
        $jory->addSort(new Sort('last_name', 'asc'));
        $jory->addSort(new Sort('full_name', 'asc'));
        $this->assertTrue($jory->hasSort('first_name'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_filter_1()
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

        $this->assertTrue($jory->hasFilter('project'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_filter_2()
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

        $this->assertFalse($jory->hasFilter('album'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_filter_3()
    {
        $parser = new ArrayParser([]);

        $jory = $parser->getJory();

        $this->assertFalse($jory->hasFilter('album'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_filter_4()
    {
        $parser = new ArrayParser([
            'filter' => [
                'field' => 'first_name',
                'data' => 'Eric',
            ],
        ]);

        $jory = $parser->getJory();

        $this->assertFalse($jory->hasFilter('last_name'));
        $this->assertTrue($jory->hasFilter('first_name'));
    }

    /** @test */
    public function it_can_tell_if_it_contains_a_filter_5()
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
                ],
            ],
        ]);

        $jory = $parser->getJory();

        $this->assertFalse($jory->hasFilter('full_name'));
        $this->assertTrue($jory->hasFilter('first_name'));
    }
}
