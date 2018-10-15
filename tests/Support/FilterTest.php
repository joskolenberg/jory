<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 08:41.
 */

namespace JosKolenberg\Jory\Tests\Support;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Filter;

class FilterTest extends TestCase
{
    /** @test */
    public function it_can_give_the_field_by_getter_function()
    {
        $filter = new Filter('name');
        $this->assertEquals('name', $filter->getField());
    }

    /** @test */
    public function it_can_give_the_field_as_an_attribute()
    {
        $filter = new Filter('name');
        $this->assertEquals('name', $filter->field);
    }

    /** @test */
    public function it_can_give_the_field_as_a_minified_attribute()
    {
        $filter = new Filter('name');
        $this->assertEquals('name', $filter->f);
    }

    /** @test */
    public function it_can_give_the_operator_by_getter_function()
    {
        $filter = new Filter('name', 'like');
        $this->assertEquals('like', $filter->getOperator());
    }

    /** @test */
    public function it_can_give_the_operator_as_an_attribute()
    {
        $filter = new Filter('name', 'like');
        $this->assertEquals('like', $filter->operator);
    }

    /** @test */
    public function it_can_give_the_operator_as_a_minified_attribute()
    {
        $filter = new Filter('name', 'like');
        $this->assertEquals('like', $filter->o);
    }

    /** @test */
    public function it_can_give_the_value_by_getter_function()
    {
        $filter = new Filter('name', 'like', 'John');
        $this->assertEquals('John', $filter->getValue());
    }

    /** @test */
    public function it_can_give_the_value_as_an_attribute()
    {
        $filter = new Filter('name', 'like', 'John');
        $this->assertEquals('John', $filter->value);
    }

    /** @test */
    public function it_can_give_the_value_as_a_minified_attribute()
    {
        $filter = new Filter('name', 'like', 'John');
        $this->assertEquals('John', $filter->v);
    }

    /** @test */
    public function an_empty_operator_defaults_to_null()
    {
        $filter = new Filter('name');
        $this->assertNull($filter->getOperator());

        $filter = new Filter('name');
        $this->assertNull($filter->operator);
    }

    /** @test */
    public function an_empty_value_defaults_to_null()
    {
        $filter = new Filter('name');
        $this->assertNull($filter->getValue());

        $filter = new Filter('name');
        $this->assertNull($filter->value);
    }

    /** @test */
    public function the_value_attribute_can_contain_mixed_data_including_arrays()
    {
        $filter = new Filter('name', '=', ['testing', [1, 2, 3]]);
        $this->assertEquals(['testing', [1, 2, 3]], $filter->getValue());
    }
}
