<?php

namespace JosKolenberg\Jory\Tests\Support;

use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Relation;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    /** @test */
    public function it_can_give_the_relation_name_by_getter_function()
    {
        $relation = new Relation('user');

        $this->assertEquals('user', $relation->getName());
    }

    /** @test */
    public function it_can_give_the_relation_name_as_an_attribute()
    {
        $relation = new Relation('user');

        $this->assertEquals('user', $relation->name);
    }

    /** @test */
    public function it_can_give_the_relation_name_as_a_minified_attribute()
    {
        $relation = new Relation('user');

        $this->assertEquals('user', $relation->n);
    }

    /** @test */
    public function it_can_give_the_jory_by_getter_function()
    {
        $relation = new Relation('user', new Jory());

        $this->assertInstanceOf(Jory::class, $relation->getJory());
    }

    /** @test */
    public function it_can_give_the_jory_as_an_attribute()
    {
        $relation = new Relation('user', new Jory());

        $this->assertInstanceOf(Jory::class, $relation->jory);
    }

    /** @test */
    public function it_can_give_the_jory_as_a_minified_attribute()
    {
        $relation = new Relation('user', new Jory());

        $this->assertInstanceOf(Jory::class, $relation->j);
    }

    /** @test */
    public function it_can_handle_a_null_value_for_jory()
    {
        $relation = new Relation('user', null);

        $this->assertNull($relation->getJory());
    }

    /** @test */
    public function it_throws_an_exception_when_an_empty_relation_name_is_given()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A relation name cannot be empty.');

        new Relation('');
    }
}
