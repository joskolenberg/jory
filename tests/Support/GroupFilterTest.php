<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 08:56
 */

namespace JosKolenberg\Jory\Tests\Support;


use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Support\GroupOrFilter;
use PHPUnit\Framework\TestCase;

class GroupFilterTest extends TestCase
{

    /** @test */
    function filters_can_be_added_to_a_group()
    {
        $group = new GroupAndFilter();
        $group->push(new Filter('name'));
        $group->push(new GroupAndFilter());
        $group->push(new GroupOrFilter());

        $this->assertEquals(3, count($group));
    }

    /** @test */
    function it_can_be_iterated()
    {
        $group = new GroupOrFilter();
        $group->push(new Filter('name'));
        $group->push(new GroupAndFilter());
        $group->push(new GroupOrFilter());

        foreach ($group as $item) {
            $this->assertInstanceOf(FilterInterface::class, $item);
        }
    }

    /** @test */
    function it_is_iterable()
    {
        $group = new GroupOrFilter();
        $group->push(new Filter('name'));
        $group->push(new GroupAndFilter());
        $group->push(new GroupOrFilter());

        foreach ($group as $item) {
            $this->assertInstanceOf(FilterInterface::class, $item);
        }
    }

    /** @test */
    function it_is_countable()
    {
        $group = new GroupOrFilter();
        $group->push(new Filter('name'));
        $group->push(new GroupAndFilter());
        $group->push(new GroupOrFilter());

        $this->assertEquals(3, count($group));
    }

    /** @test */
    function it_can_give_items_by_index()
    {
        $group = new GroupOrFilter();
        $group->push(new Filter('name'));
        $group->push(new GroupAndFilter());
        $group->push(new GroupOrFilter());

        $this->assertInstanceOf(Filter::class, $group->getByIndex(0));
        $this->assertInstanceOf(GroupAndFilter::class, $group->getByIndex(1));
        $this->assertInstanceOf(GroupOrFilter::class, $group->getByIndex(2));
    }

}