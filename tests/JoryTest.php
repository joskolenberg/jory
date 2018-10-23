<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 08:14.
 */

namespace JosKolenberg\Jory\Tests;

use JosKolenberg\Jory\Jory;
use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Sort;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\Relation;
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
        $this->assertEquals('John', $filter->getValue());
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
        $this->assertEquals(['flt' => ['f' => 'name', 'o' => '=', 'v' => 'John']], $jory->toArray());
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
                'value' => 'John',
            ],
        ], $jory->toArray(false));
    }

    /** @test */
    public function it_can_convert_itself_to_minified_json()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $this->assertEquals('{"flt":{"f":"name","o":"=","v":"John"}}', $jory->toJson());
    }

    /** @test */
    public function it_can_convert_itself_to_json()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $this->assertEquals('{"filter":{"field":"name","operator":"=","value":"John"}}', $jory->toJson(false));
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
}
