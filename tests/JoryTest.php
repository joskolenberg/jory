<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 08:14.
 */

namespace JosKolenberg\Jory\Tests;

use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\Relation;
use PHPUnit\Framework\TestCase;

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
        $filter = $jory->getFilter();
        $this->assertEquals(['flt' => ['f' => 'name', 'o' => '=', 'v' => 'John']], $jory->toArray());
    }

    /** @test */
    public function it_can_convert_itself_to_an_array()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $filter = $jory->getFilter();
        $this->assertEquals(['filter' => ['field' => 'name', 'operator' => '=', 'value' => 'John']], $jory->toArray(false));
    }

    /** @test */
    public function it_can_convert_itself_to_minified_json()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $filter = $jory->getFilter();
        $this->assertEquals('{"flt":{"f":"name","o":"=","v":"John"}}', $jory->toJson());
    }

    /** @test */
    public function it_can_convert_itself_to_json()
    {
        $jory = new Jory();
        $jory->setFilter(new Filter('name', '=', 'John'));
        $filter = $jory->getFilter();
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
}
