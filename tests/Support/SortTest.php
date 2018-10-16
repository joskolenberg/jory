<?php

namespace JosKolenberg\Jory\Tests\Support;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Sort;
use JosKolenberg\Jory\Exceptions\JoryException;

class SortTest extends TestCase
{
    /** @test */
    public function it_can_give_the_field_name_by_getter_function()
    {
        $sort = new Sort('user');
        $this->assertEquals('user', $sort->getField());
    }

    /** @test */
    public function it_can_give_the_field_name_as_an_attribute()
    {
        $sort = new Sort('user');
        $this->assertEquals('user', $sort->field);
    }

    /** @test */
    public function it_can_give_the_field_name_as_a_minified_attribute()
    {
        $sort = new Sort('user');
        $this->assertEquals('user', $sort->f);
    }

    /** @test */
    public function it_can_give_the_order_by_getter_function()
    {
        $sort = new Sort('user', 'asc');
        $this->assertEquals('asc', $sort->getOrder());
        $sort = new Sort('user', 'desc');
        $this->assertEquals('desc', $sort->getOrder());
    }

    /** @test */
    public function it_can_give_the_order_as_an_attribute()
    {
        $sort = new Sort('user', 'asc');
        $this->assertEquals('asc', $sort->order);
        $sort = new Sort('user', 'desc');
        $this->assertEquals('desc', $sort->order);
    }

    /** @test */
    public function it_can_give_the_order_as_a_minified_attribute()
    {
        $sort = new Sort('user', 'asc');
        $this->assertEquals('asc', $sort->o);
        $sort = new Sort('user', 'desc');
        $this->assertEquals('desc', $sort->o);
    }

    /** @test */
    public function it_throws_an_exception_when_no_valid_order_is_given()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sorts order can only be asc or desc.');
        new Sort('user', 'false');
    }

    /** @test */
    public function the_order_defaults_to_asc_when_none_is_given()
    {
        $sort = new Sort('users');
        $this->assertEquals('asc', $sort->getOrder());
    }
}
