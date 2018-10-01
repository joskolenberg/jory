<?php

namespace JosKolenberg\Jory\Tests\Support;


use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Relation;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{

    /** @test */
    function it_can_give_the_relation_name_by_getter_function()
    {
        $relation = new Relation('user');

        $this->assertEquals('user', $relation->getRelation());
    }

    /** @test */
    function it_can_give_the_relation_name_as_an_attribute()
    {
        $relation = new Relation('user');

        $this->assertEquals('user', $relation->relation);
    }

    /** @test */
    function it_can_give_the_relation_name_as_a_minified_attribute()
    {
        $relation = new Relation('user');

        $this->assertEquals('user', $relation->r);
    }

    /** @test */
    function it_can_give_the_jory_by_getter_function()
    {
        $relation = new Relation('user', new Jory());

        $this->assertInstanceOf(Jory::class, $relation->getJory());
    }

    /** @test */
    function it_can_give_the_jory_as_an_attribute()
    {
        $relation = new Relation('user', new Jory());

        $this->assertInstanceOf(Jory::class, $relation->jory);
    }

    /** @test */
    function it_can_give_the_jory_as_a_minified_attribute()
    {
        $relation = new Relation('user', new Jory());

        $this->assertInstanceOf(Jory::class, $relation->j);
    }

    /** @test */
    function it_can_give_the_alias_by_getter_function()
    {
        $relation = new Relation('user', new Jory(), 'active_users');

        $this->assertEquals('active_users', $relation->getAlias());
    }

    /** @test */
    function it_can_give_the_alias_as_an_attribute()
    {
        $relation = new Relation('user', new Jory(), 'active_users');

        $this->assertEquals('active_users', $relation->alias);
    }

    /** @test */
    function it_can_give_the_alias_as_a_minified_attribute()
    {
        $relation = new Relation('user', new Jory(), 'active_users');

        $this->assertEquals('active_users', $relation->a);
    }

    /** @test */
    function an_alias_is_converted_to_null_when_an_empty_string_is_used()
    {
        $relation = new Relation('user', new Jory(), '');

        $this->assertNull($relation->getAlias());
    }

    /** @test */
    function an_alias_is_converted_to_null_when_it_has_the_same_name_as_the_relation()
    {
        $relation = new Relation('user', new Jory(), 'user');

        $this->assertNull($relation->getAlias());
    }

    /** @test */
    function it_can_handle_a_null_value_for_jory()
    {
        $relation = new Relation('user', null);

        $this->assertNull($relation->getJory());
    }

    /** @test */
    function it_can_handle_a_null_value_for_alias()
    {
        $relation = new Relation('user', null);

        $this->assertNull($relation->getAlias());
    }

    /** @test */
    function it_throws_an_exception_when_an_empty_relation_name_is_given()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A relation name cannot be empty.');

        new Relation('');
    }

}